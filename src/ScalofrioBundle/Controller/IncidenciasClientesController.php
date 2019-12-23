<?php
namespace ScalofrioBundle\Controller;

use ScalofrioBundle\Entity\IncidenciasCliente;
use ScalofrioBundle\Entity\Usuarios;
use ScalofrioBundle\Form\IncidenciasClientesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;

class IncidenciasClientesController extends Controller
{
    public function incidenciaClienteNewAction()
    {
        $incidencia = new IncidenciasCliente();
        $form = $this->createIncidenciaCreateForm($incidencia);
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));

        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('form' => $form->createView(), "modo" => "nuevo", "user" => $usuario->getCliente()->getId()));
    }

    public function incidenciaClienteCreateAction(Request $request)
    {
        $incidencia = new IncidenciasCliente();
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $form = $this->createIncidenciaCreateForm($incidencia);
        $form->handleRequest($request);
        $parameters = array
        (
            "data" => array
            (
                "userName" => $this->getUser()->getUserName(),
                "data" => $form->getData(),
            ),
        );
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
            $em->persist($incidencia);
            $em->flush();
            
            $dompdf = new DOMPDF();
            $dompdf->load_html($this->renderView(
                    'ScalofrioBundle:Email:registrarIncidencia.html.twig',
                    $parameters
                    )
            );
            $dompdf->render();
            $output = $dompdf->output();
            $raiz = $this->get('kernel')->getRootDir() . '/../web/';
            $fecha = new DateTime();
            file_put_contents($raiz . 'informeIncidencia.pdf' . $fecha->getTimestamp(), $output);

            $this->sendMail($parameters, 'incidenciasclientes@controlweb.es', $raiz . 'informeIncidencia.pdf');
            if($usuario->getCliente()->getEmail() != null){
                $this->sendMail($parameters, $usuario->getCliente()->getEmail(),$raiz . 'informeIncidencia.pdf');
            }
            $this->addFlash(
                'mensaje',
                'Incidencia creada correctamente.'
            );

            return $this->redirectToRoute('scalofrio_index');
        }
        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('form' => $form->createView(), "modo" => "nuevo", "user" => $usuario->getCliente()->getId()));
    }

    public function incidenciaClienteShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $incidencia = $em->getRepository(IncidenciasCliente::class)->findOneBy(array('id' => $id));
        $form = $this->mostrarIncidenciaForm($incidencia);
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));

        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('form' => $form->createView(), "modo" => "lectura", "user" => $usuario->getCliente()->getId()));
    }

    public function actualizarEstadoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $incidencia = $em->getRepository(IncidenciasCliente::class)->findOneBy(array('id' => $id));
        if ($incidencia) {
            $estado = $incidencia->getEstado();
            $nuevoEstado = $estado === 0 ? 1 : 0;
            $incidencia->setEstado($nuevoEstado);
            $em->persist($incidencia);
            $em->flush();

            $mensaje = $nuevoEstado == 0 ? "Pendiente de resolver" : "Resuelta";
            $this->addFlash(
                'mensaje',
                'Incidencia: ' . $mensaje
            );
        } else {
            $this->addFlash(
                'mensaje',
                'Se ha producido un error al actualizar el estado.'
            );
        }
        return $this->redirectToRoute('scalofrio_listarIncidencias');
    }

    public function createIncidenciaCreateForm(IncidenciasCliente $entity)
    {
        $form = $this->createForm(new IncidenciasClientesType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_incidenciaCliente_create'),
            'method' => 'POST',
            'attr' => array("modo" => "nuevo"),
        ));

        return $form;
    }

    private function mostrarIncidenciaForm(IncidenciasCliente $entity)
    {
        $form = $this->createForm(new IncidenciasClientesType(), $entity, array("attr" => array("modo" => "lectura")));
        return $form;
    }
    private function sendMail($parameters, $mailto, $file)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Incidencia del cliente')
            ->setFrom('incidencias@controlweb.es')
            ->setTo('lcs.arjones@gmail.com')
            ->setBody(
                $this->renderView(
                    'ScalofrioBundle:Email:registrarIncidencia.html.twig',
                    $parameters
                ),
                'text/html'
            )
            ->attach(\Swift_Attachment::fromPath($file));

        $this->get('mailer')->send($message);

    }
    public function listarAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $rol = $this->getUser()->getRoles();

        if ($rol[0] == 'ROLE_USER') {
            $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
            $dql = "SELECT u FROM 
                ScalofrioBundle:IncidenciasCliente u
                WHERE u.usuario = '" . $usuario->getId() . "'
                ORDER BY u.id DESC";
        } else {
            $dql = "SELECT u FROM 
                ScalofrioBundle:IncidenciasCliente u
                ORDER BY u.id DESC";
        }

        $incidencias = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $incidencias, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:historialIncidenciaClientes.html.twig',
            array('pagination' => $pagination, 'user' => $usuario));

    }

    /* Exportar a CSV */
    public function generateAvisoCsvAction()
    {
        $em = $this->getDoctrine()->getManager();
        $avisos = $em->getRepository('ScalofrioBundle:IncidenciasCliente')->findAll();
        #Writer
        $writer = $this->container->get('egyg33k.csv.writer');
        $csv = $writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['id',
            'fecha',
            'cliente',
            'establecimiento',
            'bar',
            'tipo gestion',
            'estado',
            'descripcion'
        ]);

        foreach ($avisos as $a) {
            //Controlando si los campos son nulos.
            $subestablecimiento = ''; $establecimiento = '';
            if($a->getSubestablecimientos() != null) $subestablecimiento = $a->getSubestablecimientos()->getNombre();
            if($a->getEstablecimientos() != null) $establecimiento = $a->getEstablecimientos()->getNombre();
            //Se escribe en el CSV.
            $csv->insertOne([
                $a->getId(),
                $a->getFechaIncidencia()->format('d/m/y'),
                $a->getUsuario()->getCliente()->getNombre(),
                $establecimiento,
                $subestablecimiento,
                $a->getGestion()->getNombre(),
                $a->getEstado(),
                $a->getDescripcion(),
            ]);

        }
        $csv->output('avisos.csv');
    }

    /*OBTENCIÓN DE INFORMACIÓN EN LOS SELECTS*/
    public function obtenerSubestablecimientosClienteAction($idcliente){

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));

        $dql = "SELECT e FROM ScalofrioBundle:Subestablecimientos e
        WHERE e.cliente = '" . $idcliente . "'";

        if($usuario->getEstablecimientos() != null)
            $dql .= " AND e.establecimientos = '" . $usuario->getEstablecimientos()->getId() . "'";

        $query = $em->createQuery($dql);
        $subestab = $query->getResult();

        $select = '<option></option>';
        foreach ($subestab as $s){
            $select .= '<option value="'.$s->getId().'">'.$s->getNombre().'</option>';
        }
        return new Response($select);

    }

    public function obtenerClienteAction(){

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $select = '<option value="'.$usuario->getId().'">'.$usuario->getCliente()->getNombre().'</option>';

        return new Response($select);
    }

    public function obtenerEstablecimientoAction($idcliente){

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $select = '';
        if($usuario->getEstablecimientos() != null) {
            $select .= '<option value="' . $usuario->getEstablecimientos()->getId() . '">' . $usuario->getEstablecimientos() . '</option>';
        }else{
            $dql = "SELECT e FROM ScalofrioBundle:Establecimientos e
                    WHERE e.cliente = '" . $idcliente . "'";
            $query = $em->createQuery($dql);
            $estab = $query->getResult();

            $select = '<option></option>';
            foreach ($estab as $est) {
                $select .= '<option value="' . $est->getId() . '">' . $est->getNombre() . '</option>';
            }
        }
        return new Response($select);
    }

}
