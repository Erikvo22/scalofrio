<?php

namespace ScalofrioBundle\Controller;

use ScalofrioBundle\Entity\Cargocliente;
use ScalofrioBundle\Entity\Cliente;
use ScalofrioBundle\Entity\Comercial;
use ScalofrioBundle\Entity\Establecimientos;
use ScalofrioBundle\Entity\Gestion;
use ScalofrioBundle\Entity\Incidencias;
use ScalofrioBundle\Entity\IncidenciasCliente;
use ScalofrioBundle\Entity\Maquinas;
use ScalofrioBundle\Entity\Repuestos;
use ScalofrioBundle\Entity\Resultados;
use ScalofrioBundle\Entity\Rutas;
use ScalofrioBundle\Entity\Subestablecimientos;
use ScalofrioBundle\Entity\Usuarios;
use ScalofrioBundle\Form\ClienteType;
use ScalofrioBundle\Form\ComercialType;
use ScalofrioBundle\Form\EstablecimientosType;
use ScalofrioBundle\Form\GestionType;
use ScalofrioBundle\Form\IncidenciasType;
use ScalofrioBundle\Form\MaquinasType;
use ScalofrioBundle\Form\RepuestosType;
use ScalofrioBundle\Form\SubestablecimientosType;
use ScalofrioBundle\Form\UsuariosType;
use ScalofrioBundle\Form\ResultadosType;
use ScalofrioBundle\Form\RutasType;
use ScalofrioBundle\Form\CargoclienteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;

class UserController extends Controller
{
    public function homeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = $this->getUser()->getRoles();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $cliente = $em->getRepository(Cliente::class)->find($usuario->getCliente()->getId());

        $incCliPend = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => 0
        ));
        $incCliPend2 = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => null
        ));
        $sumaP = count($incCliPend) + count($incCliPend2);
        $incRev = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => 0,
            'testigo' => 1
        ));
        $incRev2 = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => null,
            'testigo' => 1
        ));
        $sumaR = count($incRev) + count($incRev2);

        if ($rol[0] == 'ROLE_ADMIN' || $rol[0] == 'ROLE_COMERCIAL') {
            $dql = "SELECT u FROM ScalofrioBundle:Incidencias u ORDER BY u.id DESC";
        } else {
            $dql = "SELECT u FROM
                ScalofrioBundle:IncidenciasCliente u
                WHERE u.usuario = '" . $usuario->getId() . "'
                ORDER BY u.id DESC";
            $dqlVisitas = "SELECT u FROM 
                ScalofrioBundle:Incidencias u
                WHERE u.cliente = '" . $cliente->getId() . "'";
            if($usuario->getEstablecimientos() != null) {
                $dqlVisitas .= " AND u.establecimientos = '" . $usuario->getEstablecimientos()->getId() . "'";
            }
            $dqlVisitas .= "ORDER BY u.id DESC";

            $incidenciasVisitas = $em->createQuery($dqlVisitas);
            $paginatorVisitas = $this->get('knp_paginator');
            $paginationVisitas = $paginatorVisitas->paginate(
                $incidenciasVisitas, $request->query->getInt('page', 1),
                10
            );
        }

        $incidencias = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $incidencias, $request->query->getInt('page', 1),
            10
        );
        if ($rol[0] == 'ROLE_ADMIN' || $rol[0] == 'ROLE_COMERCIAL') {
            return $this->render('ScalofrioBundle:User:index.html.twig',
                array('pagination' => $pagination, 'incCliPend' => $sumaP, 'incRev' => $sumaR, 'user' => $usuario));
        } else {
            return $this->render('ScalofrioBundle:User:historialIncidenciaClientes.html.twig',
                array('pagination' => $pagination,'paginationVisitas' => $paginationVisitas,'user' => $usuario));
        }
    }

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = $this->getUser()->getRoles();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $cliente = $em->getRepository(Cliente::class)->find($usuario->getCliente()->getId());

        $incCliPend = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => 0
        ));
        $incCliPend2 = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => null
        ));
        $sumaP = count($incCliPend) + count($incCliPend2);
        $incRev = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => 0,
            'testigo' => 1
        ));
        $incRev2 = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => null,
            'testigo' => 1
        ));
        $sumaR = count($incRev) + count($incRev2);

        if ($rol[0] == 'ROLE_ADMIN' || $rol[0] == 'ROLE_COMERCIAL') {
            $dql = "SELECT u FROM ScalofrioBundle:Incidencias u ORDER BY u.id DESC";
        } else {
            $dql = "SELECT u FROM
                ScalofrioBundle:IncidenciasCliente u
                WHERE u.usuario = '" . $usuario->getId() . "'
                ORDER BY u.id DESC";
            $dqlVisitas = "SELECT u FROM 
                ScalofrioBundle:Incidencias u
                WHERE u.cliente = '" . $cliente->getId() . "'";
            if($usuario->getEstablecimientos() != null) {
                $dqlVisitas .= " AND u.establecimientos = '" . $usuario->getEstablecimientos()->getId() . "'";
            }
            $dqlVisitas .= "ORDER BY u.id DESC";

            $incidenciasVisitas = $em->createQuery($dqlVisitas);
            $paginatorVisitas = $this->get('knp_paginator');
            $paginationVisitas = $paginatorVisitas->paginate(
                $incidenciasVisitas, $request->query->getInt('page', 1),
                10
            );
        }

        $incidencias = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $incidencias, $request->query->getInt('page', 1),
            10
        );
        if ($rol[0] == 'ROLE_ADMIN' || $rol[0] == 'ROLE_COMERCIAL') {
            return $this->render('ScalofrioBundle:User:index.html.twig',
                array('pagination' => $pagination, 'incCliPend' => $sumaP, 'incRev' => $sumaR, 'user' => $usuario));
        } else {
            return $this->render('ScalofrioBundle:User:historialIncidenciaClientes.html.twig',
                array('pagination' => $pagination,'paginationVisitas' => $paginationVisitas,'user' => $usuario));
        }
    }

    /******** APARTADO DE INCIDENCIAS **********/

    public function incidenciaAddAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $comercial = null;
        if($usuario->getComercial() != null) {
            $comercial = $usuario->getComercial()->getId();
        }
        $incidencia = new Incidencias();
        $form = $this->createIncidenciaCreateForm($incidencia);

        return $this->render('ScalofrioBundle:User:incidenciaAdd.html.twig', array('form' => $form->createView(), 'user' => $usuario,
                                'comercial' => $comercial));
    }

    private function createIncidenciaCreateForm(Incidencias $entity)
    {
        $form = $this->createForm(new IncidenciasType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_incidencia_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createIncidenciaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $comercial = null;
        if($usuario->getComercial() != null) {
            $comercial = $usuario->getComercial()->getId();
        }
        $incidencia = new Incidencias();
        $form = $this->createIncidenciaCreateForm($incidencia);
        $form->handleRequest($request);

        //Modificamos el valor del campo 'testigo' en incidencias_clientes en caso de que se haya puesto un número de incidencia.
        if($incidencia->getNumIncCliente()!=null){
            $incCliente = $em->getRepository('ScalofrioBundle:IncidenciasCliente')->find($incidencia->getNumIncCliente()->getId());
            $incCliente->setTestigo(1);
            $em->persist($incCliente);
            $em->flush();
        }

        if ($form->isValid()) {
            $em->persist($incidencia);
            $em->flush();

            /* TEXTO PARA EL ENVÍO DE EMAIL*/
            //Controlando si los campos son nulos.
            // $establecimiento = '';$comercial = '';$cliente = '';$gestion = '';$maquinas = '';$repuestos = '';
            $datos = array();
            if($incidencia->getNumIncCliente() != null){
                $datos["numinccliente"] = $incidencia->getNumIncCliente()->getId();
            }

            if ($incidencia->getEstablecimientos() != null) {
                $datos["establecimientos"] = $incidencia->getEstablecimientos()->getNombre();
            }

            if ($incidencia->getSubestablecimientos() != null) {
                $datos["subestablecimientos"] = $incidencia->getSubestablecimientos()->getNombre();
            }

            if ($incidencia->getRuta() != null) {
                $datos["ruta"] = $incidencia->getRuta();
            }

            if ($incidencia->getComercial() != null) {
                $datos["comercial"] = $incidencia->getComercial()->getNombre();
            }

            if ($incidencia->getCliente() != null) {
                $datos["cliente"] = $incidencia->getCliente()->getNombre();
            }

            if ($incidencia->getGestion() != null) {
                $datos["gestion"] = $incidencia->getGestion()->getNombre();
            }

            if ($incidencia->getResultado() != null) {
                $datos["resultado"] = $incidencia->getResultado();
            }

            if ($incidencia->getMaquinas() != null) {
                $datos["maquinas"] = $incidencia->getMaquinas()->getNombre();
            }

            if ($incidencia->getRepuestos() != null) {
                $datos["repuestos"] = $incidencia->getRepuestos();
            }

            $datos["incidencia"] = $incidencia;

            $datos["firma" ]= $incidencia->getFirma();
            if($datos["firma"] == null){
                return $this->render('ScalofrioBundle:User:incidenciaAdd.html.twig', array('form' => $form->createView(),
                    'user' => $usuario, 'comercial' => $comercial));
            }

            /* COMPROBAMOS SI EL CLIENTE TIENE UN EMAIL REGISTRADO Y SI SE HA PUESTO ALGUNO EN LA INCIDENCIA */
            $emailCliente = "";
            $emailPlus = "";
            if ($incidencia->getCliente()->getEmail() != null) {
                $emailCliente = $incidencia->getCliente()->getEmail();
            }

            if ($incidencia->getEmail() != null) {
                $emailPlus = $incidencia->getEmail();
            }

            try {

                    
                    $dompdf = new DOMPDF();
                    $dompdf->load_html($this->renderView(
                            'ScalofrioBundle:Email:registrarIncidenciaAdministrador.html.twig',
                            array('datos' => $datos)
                            )
                    );
                    $dompdf->render();
                    $output = $dompdf->output();
                    $raiz = $this->get('kernel')->getRootDir() . '/../web/';
                    $fecha = new \DateTime();
                    $nombrePdf = 'INCIDENCIA ' . $incidencia->getCliente()->getNombre() . ' - ' . $incidencia->getFecha()->format('d-m-Y').'.pdf';
                    file_put_contents($raiz . $nombrePdf, $output);
                    $file = $raiz . $nombrePdf;
        

                $message = \Swift_Message::newInstance()
                    ->setSubject('INCIDENCIA SCALOFRIO S.L. - ' . $incidencia->getCliente()->getNombre() . ' - ' . $incidencia->getFecha()->format('d/m/y'))
                    ->setFrom('incidencias@controlweb.es')
                    ->setTo('incidenciascomerciales@controlweb.es')
                    ->setBody(
                        $this->renderView(
                            'ScalofrioBundle:Email:registrarIncidenciaAdministrador.html.twig',
                            array(
                                "datos" => $datos
                            )
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath($file));
                    // ->attach(\Swift_Attachment::fromPath($incidencia->getFirma(), 'image/png')->setFilename('firmacliente.png'));

                $this->get('mailer')->send($message);

                if($emailCliente != ""){
                    $message = \Swift_Message::newInstance()
                        ->setSubject('INCIDENCIA SCALOFRIO S.L. - ' . $incidencia->getCliente()->getNombre() . ' - ' . $incidencia->getFecha()->format('d/m/y'))
                        ->setFrom('incidencias@controlweb.es')
                        ->setTo($emailCliente)
                        ->setBody(
                            $this->renderView(
                                'ScalofrioBundle:Email:registrarIncidenciaAdministrador.html.twig',
                                array(
                                    "datos" => $datos
                                )
                            ),
                            'text/html'
                        )
                        ->attach(\Swift_Attachment::fromPath($file));
                        // ->attach(\Swift_Attachment::fromPath($incidencia->getFirma(), 'image/png')->setFilename('firmacliente.png'));

                    $this->get('mailer')->send($message);
                }

                if($emailPlus != ""){
                    $message = \Swift_Message::newInstance()
                        ->setSubject('INCIDENCIA SCALOFRIO S.L. - ' . $incidencia->getCliente()->getNombre() . ' - ' . $incidencia->getFecha()->format('d/m/y'))
                        ->setFrom('incidencias@controlweb.es')
                        ->setTo($emailPlus)
                        ->setBody(
                            $this->renderView(
                                'ScalofrioBundle:Email:registrarIncidenciaAdministrador.html.twig',
                                array(
                                    "datos" => $datos
                                )
                            ),
                            'text/html'
                        )
                        ->attach(\Swift_Attachment::fromPath($file));
                        // ->attach(\Swift_Attachment::fromPath($incidencia->getFirma(), 'image/png')->setFilename('firmacliente.png'));

                    $this->get('mailer')->send($message);
                }

            } catch (\Exception $e) {
                throw $e;
            }

            $this->addFlash(
                'mensaje',
                'Incidencia creada correctamente.'
            );

            $firmaRuta = $incidencia->getFirma();
            $firmaArray = split(',', $firmaRuta);
            $firmaBase64 = $firmaArray[1];
            $incidencia->setFirma($firmaBase64);
            $em->persist($incidencia);
            $em->flush();

            return $this->redirectToRoute('scalofrio_index');
        }
        return $this->render('ScalofrioBundle:User:incidenciaAdd.html.twig', array('form' => $form->createView(), 'user' => $usuario));
    }

    /*/# EDICIÓN #/*/

    public function incidenciaEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $incidencia = $em->getRepository('ScalofrioBundle:Incidencias')->find($id);

        if (!$incidencia) {
            $messageException = 'Incidencia no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createIncidenciaEditForm($incidencia);

        return $this->render('ScalofrioBundle:User:incidenciaEdit.html.twig', array('incidencia' => $incidencia,
                            'form' => $form->createView(), 'user' => $usuario));
    }

    private function createIncidenciaEditForm(Incidencias $entity)
    {
        $form = $this->createForm(new IncidenciasType(), $entity, array('action' => $this->generateUrl('scalofrio_incidencia_update',
            array('id' => $entity->getId())), 'method' => 'PUT'));
        return $form;
    }

    public function incidenciaUpdateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $incidencia = $em->getRepository('ScalofrioBundle:Incidencias')->find($id);

        if (!$incidencia) {
            $messageException = 'Incidencia no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createIncidenciaEditForm($incidencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $successMessage = 'Incidencia actualizada correctamente';
            $this->addFlash('mensaje', $successMessage);
            return $this->redirectToRoute('scalofrio_index');
        }
        return $this->render('ScalofrioBundle:User:incidenciaEdit.html.twig', array('incidencia' => $incidencia,
                            'form' => $form->createView(), 'user' => $usuario));
    }

    /*VER*/
    public function incidenciaViewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $incidencia = $em->getRepository('ScalofrioBundle:Incidencias')->find($id);
        $repuestosIncidencia = $em->getRepository('ScalofrioBundle:Incidencias_repuestos')->findBy(
            array(
                'incidenciasId' => $id,
            )
        );

        $cont = 0;
        $repuestos = [];
        foreach ($repuestosIncidencia as $r) {
            $b = $em->getRepository('ScalofrioBundle:Repuestos')->find($r->getRepuestosId());
            $repuestos[$cont] = $b->getNombre();
            $cont++;
        }

        if (!$incidencia) {
            $messageException = 'Incidencia no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        $deleteForm = $this->createIncidenciaDeleteForm($incidencia);

        return $this->render('@Scalofrio/User/incidenciaView.html.twig', array('incidencia' => $incidencia,
                            'repuestos' => $repuestos, 'delete_form' => $deleteForm->createView(), 'user' => $usuario));
    }

    /*ELIMINAR*/
    private function createIncidenciaDeleteForm($incidencia)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('scalofrio_incidencia_delete', array('id' => $incidencia->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function incidenciaDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $incidencia = $em->getRepository('ScalofrioBundle:Incidencias')->find($id);

        if (!$incidencia) {
            $messageException = 'Incidencia no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createIncidenciaDeleteForm($incidencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($incidencia);
            $em->flush();

            /*Comprobamos si existe otra incidencia con el numinccliente, si no, testigo = 0 */
            $incNum = 0;
            if($incidencia->getNumIncCliente() != null) {
                $incNum = $em->getRepository('ScalofrioBundle:Incidencias')->findBy(
                    array(
                        'numinccliente' => $incidencia->getNumIncCliente()->getId(),
                    )
                );
            }
            if(count($incNum) == 0){
                $incCliente = $em->getRepository('ScalofrioBundle:IncidenciasCliente')->find($incidencia->getNumIncCliente()->getId());
                $incCliente->setTestigo(0);
                $em->persist($incCliente);
                $em->flush();
            }

            $successMessage = 'Incidencia eliminada correctamente';
            $this->addFlash('mensaje', $successMessage);
            return $this->redirectToRoute('scalofrio_index');
        }
    }

    /******** EXPORTAR A CSV **********/

    public function generateCsvAction()
    {
        $em = $this->getDoctrine()->getManager();
        $incidencias = $em->getRepository('ScalofrioBundle:Incidencias')->findAll();
        #Writer
        $writer = $this->container->get('egyg33k.csv.writer');
        $csv = $writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['id',
            'Fecha',
            'Comercial',
            'Cliente',
            'Establecimiento',
            'Bar',
            'Ruta',
            'Nombre cliente',
            'Cargo cliente',
            'Gestion',
            'Resultado',
            'Tiempo(min)',
            'Maquinas',
        ]);

        foreach ($incidencias as $incidencia) {
            //Controlando si los campos son nulos.
            $establecimiento = '';
            $subestablecimiento = '';
            $comercial = '';
            $cliente = '';
            $gestion = '';
            $maquinas = '';
            $repuestos = '';

            if ($incidencia->getEstablecimientos() != null) {
                $establecimiento = $incidencia->getEstablecimientos()->getNombre();
            }

            if ($incidencia->getSubestablecimientos() != null) {
                $subestablecimiento = $incidencia->getSubestablecimientos()->getNombre();
            }

            if ($incidencia->getComercial() != null) {
                $comercial = $incidencia->getComercial()->getNombre();
            }

            if ($incidencia->getCliente() != null) {
                $cliente = $incidencia->getCliente()->getNombre();
            }

            if ($incidencia->getGestion() != null) {
                $gestion = $incidencia->getGestion()->getNombre();
            }

            if ($incidencia->getMaquinas() != null) {
                $maquinas = $incidencia->getMaquinas()->getNombre();
                //Las incidencias pueden tener mas de un repuesto
                $repuestos = $incidencia->getRepuestos();
            }

            //Se escribe en el CSV.
            $csv->insertOne([
                $incidencia->getId(),
                $incidencia->getFecha()->format('d/m/y'),
                $comercial,
                $cliente,
                $establecimiento,
                $subestablecimiento,
                $incidencia->getRuta(),
                $incidencia->getNombrecliente(),
                $incidencia->getCargocliente(),
                $gestion,
                $incidencia->getResultado(),
                $incidencia->getTiempo(),
                $maquinas,
            ]);

            foreach ($repuestos as $r) {
                $csv->insertOne([
                    $incidencia->getId(),
                    $r->getNombre(),
                ]);
            }
        }
        $csv->output('incidencias.csv');
        die;
    }

    public function generateUserCsvAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('ScalofrioBundle:Usuarios')->findAll();
        #Writer
        $writer = $this->container->get('egyg33k.csv.writer');
        $csv = $writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['id',
            'Cliente',
            'Establecimiento',
            'Nombre de usuario',
            'Role',
            'Activo',
        ]);

        foreach ($usuarios as $user) {
            //Controlando si los campos son nulos.
            $establecimiento = '';
            $cliente = '';
            if ($user->getEstablecimientos() != null) {
                $establecimiento = $user->getEstablecimientos()->getNombre();
            }

            if ($user->getCliente() != null) {
                $cliente = $user->getCliente()->getNombre();
            }

            if ($user->getIsActive() == 0) {
                $activo = 'No';
            } else{
                $activo = 'Si';
            }

            if ($user->getRole() == 'ROLE_ADMIN') {
                $role = 'Administrador';
            } else if($user->getRole() == 'ROLE_COMERCIAL'){
                $role = 'Comercial';
            } else{
                $role = 'Usuario';
            }

            //Se escribe en el CSV.
            $csv->insertOne([
                $user->getId(),
                $cliente,
                $establecimiento,
                $user->getUsername(),
                $role,
                $activo,
            ]);

        }
        $csv->output('usuarios.csv');
        die;
    }

    /******** APARTADO DE USUARIOS **********/

    public function userListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $dql = "SELECT u FROM ScalofrioBundle:Usuarios u order by u.username ASC";
        $usuarios = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $usuarios, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:userList.html.twig', array('pagination' => $pagination,
                                'user' => $usuario));
    }

    public function userAddAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $user = new Usuarios();
        $form = $this->createUserCreateForm($user);

        return $this->render('ScalofrioBundle:User:userAdd.html.twig', array('form' => $form->createView(),
                                'user' => $usuario));
    }

    private function createUserCreateForm(Usuarios $entity)
    {
        $form = $this->createForm(new UsuariosType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_user_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $user = new Usuarios();
        $form = $this->createUserCreateForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $password = $form->get('password')->getData();
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            $user->setPassword($encoded);
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Usuario creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_user_list');
        }
        return $this->render('ScalofrioBundle:User:userAdd.html.twig', array('form' => $form->createView(),
                                'user' => $usuario));
    }

    public function userEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = $this->getUser()->getRoles();
        $user = $em->getRepository('ScalofrioBundle:Usuarios')->find($id);

        if (!$user) {
            $messageException = 'Usuario no encontrado.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createUserEditForm($user);

        if ($rol[0] == 'ROLE_USER' || $rol[0] == 'ROLE_COMERCIAL') {
            if($id == $this->getUser()->getId()) {
                return $this->render('ScalofrioBundle:User:userUserEdit.html.twig', array('user' => $user
                , 'form' => $form->createView()));
            }else{
                return $this->redirectToRoute('scalofrio_index');
            }
        }else {
            return $this->render('ScalofrioBundle:User:userEdit.html.twig', array('user' => $user
            , 'form' => $form->createView()));
        }
    }

    private function createUserEditForm(Usuarios $entity)
    {
        $form = $this->createForm(new UsuariosType(), $entity, array('action' => $this->generateUrl('scalofrio_user_update',
            array('id' => $entity->getId())), 'method' => 'PUT'));
        return $form;
    }

    public function userUpdateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = $this->getUser()->getRoles();
        $user = $em->getRepository('ScalofrioBundle:Usuarios')->find($id);

        if (!$user) {
            $messageException = 'Usuario no encontrado.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createUserEditForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            if (!empty($password)) {
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password);
                $user->setPassword($encoded);
            } else {
                $recoverPass = $this->recoverPass($id);
                $user->setPassword($recoverPass[0]['password']);
            }

            if ($form->get('role')->getData() == 'ROLE_ADMIN') {
                $user->setIsActive(1);
            }
            $em->flush();
            if($rol[0] == 'ROLE_USER' || $rol[0] == 'ROLE_COMERCIAL'){
                $successMessage = 'Se ha actualizado su perfil correctamente';
                $this->addFlash('mensaje', $successMessage);
                return $this->redirectToRoute('scalofrio_index');
            }else{
                $successMessage = 'Usuario actualizado correctamente';
                $this->addFlash('mensaje', $successMessage);
                return $this->redirectToRoute('scalofrio_user_list', array('id' => $user->getId()));
            }
        }
        return $this->render('ScalofrioBundle:User:userEdit.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    private function recoverPass($id)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT u.password
            FROM ScalofrioBundle:Usuarios u
            WHERE u.id = :id'
        )->setParameter('id', $id);

        $currentPass = $query->getResult();

        return $currentPass;
    }

    /******** APARTADO DE COMERCIALES **********/

    public function comercialAddAction(Request $request)
    {
        $comercial = new Comercial();
        $form = $this->createComercialCreateForm($comercial);

        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM ScalofrioBundle:Comercial u order by u.nombre ASC";
        $comerciales = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $comerciales, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:comercialAdd.html.twig', array('form' => $form->createView(),
                            'comercial' => $comerciales, 'pagination' => $pagination));
    }

    private function createComercialCreateForm(Comercial $entity)
    {
        $form = $this->createForm(new ComercialType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_comercial_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createComercialAction(Request $request)
    {
        $comercial = new Comercial();
        $form = $this->createComercialCreateForm($comercial);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comercial);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Comercial creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_comercial_add');
        }
        return $this->render('ScalofrioBundle:User:comercialAdd.html.twig', array('form' => $form->createView()));
    }

    /******** APARTADO DE CLIENTES **********/

    public function clienteListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT u FROM ScalofrioBundle:Cliente u order by u.nombre ASC";
        $clientes = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $clientes, $request->query->getInt('page', 1),
            10
        );

        //Añadir nuevo establecimiento
        $establecimientos = new Establecimientos();
        $estab = $this->createEstablecimientosCreateForm($establecimientos);

        //Añadir nuevo Subestablecimiento
        $subestablecimientos = new Subestablecimientos();
        $subestab = $this->createSubestablecimientosCreateForm($subestablecimientos);

        return $this->render('ScalofrioBundle:User:clienteList.html.twig', array('pagination' => $pagination, 'estab' => $estab->createView(), 'subestab' => $subestab->createView()));
    }

    public function clienteViewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('ScalofrioBundle:Cliente');
        $cliente = $repository->find($id);
        $establecimientos = $em->getRepository('ScalofrioBundle:Establecimientos')->findBy(
            array(
                'cliente' => $id,
            )
        );
        $subestablecimientos = $em->getRepository('ScalofrioBundle:Subestablecimientos')->findBy(
            array(
                'cliente' => $id,
            )
        );

        if (!$cliente) {
            $messageException = 'Cliente no encontrado.';
            throw $this->createNotFoundException($messageException);
        }

        return $this->render('@Scalofrio/User/clienteView.html.twig', array('cliente' => $cliente, 'establecimientos' => $establecimientos, 'subestablecimientos' => $subestablecimientos));
    }

    private function createEstablecimientosCreateForm(Establecimientos $entity)
    {
        $form = $this->createForm(new EstablecimientosType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_establecimientos_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createEstablecimientosAction(Request $request)
    {
        $establecimiento = new Establecimientos();
        $form = $this->createEstablecimientosCreateForm($establecimiento);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($establecimiento);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Establecimiento creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_cliente_list');
        }
        return $this->render('ScalofrioBundle:User:clienteView.html.twig', array('form' => $form->createView()));
    }

    private function createSubestablecimientosCreateForm(Subestablecimientos $entity)
    {
        $form = $this->createForm(new SubestablecimientosType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_subestablecimientos_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createSubestablecimientosAction(Request $request)
    {
        $subestablecimientos = new Subestablecimientos();
        $form = $this->createSubestablecimientosCreateForm($subestablecimientos);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subestablecimientos);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Subestablecimiento creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_cliente_list');
        }
        return $this->render('ScalofrioBundle:User:clienteView.html.twig', array('form' => $form->createView()));
    }

    public function clienteAddAction()
    {
        $cliente = new Cliente();
        $form = $this->createClienteCreateForm($cliente);

        return $this->render('ScalofrioBundle:User:clienteAdd.html.twig', array('form' => $form->createView()));
    }

    private function createClienteCreateForm(Cliente $entity)
    {
        $form = $this->createForm(new ClienteType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_cliente_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createClienteAction(Request $request)
    {
        $cliente = new Cliente();
        $form = $this->createClienteCreateForm($cliente);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cliente);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Cliente creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_cliente_list');
        }
        return $this->render('ScalofrioBundle:User:clienteAdd.html.twig', array('form' => $form->createView()));
    }

    public function clienteEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $cliente = $em->getRepository('ScalofrioBundle:Cliente')->find($id);

        if (!$cliente) {
            $messageException = 'Cliente no encontrado.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createClienteEditForm($cliente);

        return $this->render('ScalofrioBundle:User:clienteEdit.html.twig', array('cliente' => $cliente, 'form' => $form->createView()));
    }

    private function createClienteEditForm(Cliente $entity)
    {
        $form = $this->createForm(new ClienteType(), $entity, array('action' => $this->generateUrl('scalofrio_cliente_update',
            array('id' => $entity->getId())), 'method' => 'PUT'));
        return $form;
    }

    public function clienteUpdateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cliente = $em->getRepository('ScalofrioBundle:Cliente')->find($id);

        if (!$cliente) {
            $messageException = 'Cliente no encontrado.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createClienteEditForm($cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $successMessage = 'Cliente actualizado correctamente';
            $this->addFlash('mensaje', $successMessage);
            return $this->redirectToRoute('scalofrio_cliente_list', array('id' => $cliente->getId()));
        }
        return $this->render('ScalofrioBundle:User:clienteEdit.html.twig', array('cliente' => $cliente, 'form' => $form->createView()));
    }

    /******** APARTADO DE MAQUINAS **********/

    public function maquinasListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT u FROM ScalofrioBundle:Maquinas u order by u.nombre ASC";
        $maquinas = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $maquinas, $request->query->getInt('page', 1),
            10
        );

        //Añadir nuevos repuestos
        $repuestos = new Repuestos();
        $form = $this->createRepuestosCreateForm($repuestos);

        return $this->render('ScalofrioBundle:User:maquinasList.html.twig', array('pagination' => $pagination, 'form' => $form->createView()));
    }

    public function maquinasViewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('ScalofrioBundle:Maquinas');
        $maquina = $repository->find($id);
        $repuestos = $em->getRepository('ScalofrioBundle:Repuestos')->findBy(
            array(
                'maquinas' => $id,
            )
        );

        if (!$maquina) {
            $messageException = 'Máquina no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        return $this->render('@Scalofrio/User/maquinasView.html.twig', array('maquina' => $maquina, 'repuestos' => $repuestos));
    }

    private function createRepuestosCreateForm(Repuestos $entity)
    {
        $form = $this->createForm(new RepuestosType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_repuestos_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createRepuestosAction(Request $request)
    {
        $repuesto = new Repuestos();
        $form = $this->createRepuestosCreateForm($repuesto);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($repuesto);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Repuesto creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_maquinas_list');
        }
        return $this->render('ScalofrioBundle:User:maquinasView.html.twig', array('form' => $form->createView()));
    }

    public function maquinasAddAction()
    {
        $maquinas = new Maquinas();
        $form = $this->createMaquinasCreateForm($maquinas);

        return $this->render('ScalofrioBundle:User:maquinasAdd.html.twig', array('form' => $form->createView()));
    }

    private function createMaquinasCreateForm(Maquinas $entity)
    {
        $form = $this->createForm(new MaquinasType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_maquinas_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createMaquinasAction(Request $request)
    {
        $maquina = new Maquinas();
        $form = $this->createMaquinasCreateForm($maquina);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($maquina);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Máquina creada correctamente'
            );

            return $this->redirectToRoute('scalofrio_maquinas_list');
        }
        return $this->render('ScalofrioBundle:User:maquinasAdd.html.twig', array('form' => $form->createView()));
    }

    public function maquinasEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $maquina = $em->getRepository('ScalofrioBundle:Maquinas')->find($id);

        if (!$maquina) {
            $messageException = 'Máquina no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createMaquinasEditForm($maquina);

        return $this->render('ScalofrioBundle:User:maquinasEdit.html.twig', array('maquina' => $maquina, 'form' => $form->createView()));
    }

    private function createMaquinasEditForm(Maquinas $entity)
    {
        $form = $this->createForm(new MaquinasType(), $entity, array('action' => $this->generateUrl('scalofrio_maquinas_update',
            array('id' => $entity->getId())), 'method' => 'PUT'));
        return $form;
    }

    public function maquinasUpdateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $maquina = $em->getRepository('ScalofrioBundle:Maquinas')->find($id);

        if (!$maquina) {
            $messageException = 'Máquina no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createMaquinasEditForm($maquina);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $successMessage = 'Máquina actualizada correctamente';
            $this->addFlash('mensaje', $successMessage);
            return $this->redirectToRoute('scalofrio_maquinas_list', array('id' => $maquina->getId()));
        }
        return $this->render('ScalofrioBundle:User:maquinasEdit.html.twig', array('maquina' => $maquina, 'form' => $form->createView()));
    }

    /******** APARTADO DE GESTIONES **********/

    public function gestionAddAction(Request $request)
    {
        $gestion = new Gestion();
        $form = $this->createGestionCreateForm($gestion);

        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM ScalofrioBundle:Gestion u order by u.nombre ASC";
        $gestiones = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $gestiones, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:gestionAdd.html.twig', array('form' => $form->createView(),
                            'gestion' => $gestiones, 'pagination' => $pagination));
    }

    private function createGestionCreateForm(Gestion $entity)
    {
        $form = $this->createForm(new GestionType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_gestion_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createGestionAction(Request $request)
    {
        $gestion = new Gestion();
        $form = $this->createGestionCreateForm($gestion);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($gestion);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Nueva gestión creada correctamente'
            );

            return $this->redirectToRoute('scalofrio_gestion_add');
        }
        return $this->render('ScalofrioBundle:User:gestionAdd.html.twig', array('form' => $form->createView()));
    }

    /******** APARTADO DE RESULTADOS **********/

    public function resultadosAddAction(Request $request)
    {
        $resultados = new Resultados();
        $form = $this->createResultadosCreateForm($resultados);

        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM ScalofrioBundle:Resultados u order by u.nombre ASC";
        $resultado = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $resultado, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:resultadosAdd.html.twig', array('form' => $form->createView(),
                            'resultado' => $resultado, 'pagination' => $pagination));
    }

    private function createResultadosCreateForm(Resultados $entity)
    {
        $form = $this->createForm(new ResultadosType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_resultados_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createResultadosAction(Request $request)
    {
        $resultados = new Resultados();
        $form = $this->createResultadosCreateForm($resultados);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($resultados);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Nuevo tipo de resultado creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_resultados_add');
        }
        return $this->render('ScalofrioBundle:User:resultadoAdd.html.twig', array('form' => $form->createView()));
    }

    /******** APARTADO DE RUTAS **********/

    public function rutasAddAction(Request $request)
    {
        $rutas = new Rutas();
        $form = $this->createRutasCreateForm($rutas);

        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM ScalofrioBundle:Rutas u order by u.nombre ASC";
        $ruta = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $ruta, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:rutasAdd.html.twig', array('form' => $form->createView(),
                            'ruta' => $ruta, 'pagination' => $pagination));
    }

    private function createRutasCreateForm(Rutas $entity)
    {
        $form = $this->createForm(new RutasType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_rutas_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createRutasAction(Request $request)
    {
        $rutas = new Rutas();
        $form = $this->createRutasCreateForm($rutas);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rutas);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Nueva ruta creada correctamente'
            );

            return $this->redirectToRoute('scalofrio_rutas_add');
        }
        return $this->render('ScalofrioBundle:User:rutasAdd.html.twig', array('form' => $form->createView()));
    }

    /******** APARTADO DE CARGOS DEL CLIENTE **********/

    public function cargosAddAction(Request $request)
    {
        $cargos = new Cargocliente();
        $form = $this->createCargosCreateForm($cargos);

        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM ScalofrioBundle:Cargocliente u order by u.nombre ASC";
        $cargosCliente = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $cargosCliente, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:cargosAdd.html.twig', array('form' => $form->createView(),
                            'cargo' => $cargosCliente, 'pagination' => $pagination));
    }

    private function createCargosCreateForm(Cargocliente $entity)
    {
        $form = $this->createForm(new CargoclienteType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_cargos_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function createCargosAction(Request $request)
    {
        $cargos = new Cargocliente();
        $form = $this->createCargosCreateForm($cargos);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cargos);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Nuevo tipo de cargo del cliente creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_cargos_add');
        }
        return $this->render('ScalofrioBundle:User:cargosAdd.html.twig', array('form' => $form->createView()));
    }

    /******** BÚSQUEDA **********/

    public function busquedaAction(Request $request)
    {

        $busqueda = trim($_POST['buscar']);
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));
        $incCliPend = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => 0
        ));
        $incCliPend2 = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => null
        ));
        $sumaP = count($incCliPend) + count($incCliPend2);
        $incRev = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => 0,
            'testigo' => 1
        ));
        $incRev2 = $em->getRepository(IncidenciasCliente::class)->findBy(array(
            'estado' => null,
            'testigo' => 1
        ));
        $sumaR = count($incRev) + count($incRev2);

        $dql = "SELECT i FROM ScalofrioBundle:Incidencias i
        JOIN i.cliente c
        JOIN i.establecimientos e
        JOIN i.comercial co
        WHERE c.nombre LIKE '%" . $busqueda . "%'
        OR e.nombre LIKE '%" . $busqueda . "%'
        OR co.nombre LIKE '%" . $busqueda . "%'
        ORDER BY i.id DESC";
        $prod = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(

            $prod,
            $request->query->getInt('page', 1),
            10

        );

        if (count($pagination->getItems()) == 0) {
            $dql = "SELECT i FROM ScalofrioBundle:Incidencias i
                    JOIN i.cliente c
                    WHERE c.nombre LIKE '%" . $busqueda . "%'
                    ORDER BY i.id";
            $prod = $em->createQuery($dql);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(

                $prod,
                $request->query->getInt('page', 1),
                10

            );
        }

        return $this->render('ScalofrioBundle:User:index.html.twig', array('pagination' => $pagination,
            'user' => $usuario, 'incCliPend' => $sumaP, 'incRev' => $sumaR));
    }

    public function busquedaClienteAction(Request $request)
    {

        $busqueda = trim($_POST['buscar']);
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));

        $dql = "SELECT c FROM ScalofrioBundle:Cliente c
        WHERE c.nombre LIKE '%" . $busqueda . "%'
        ORDER BY c.nombre";
        $prod = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(

            $prod,
            $request->query->getInt('page', 1),
            10

        );

        //Añadir nuevo establecimiento
        $establecimientos = new Establecimientos();
        $estab = $this->createEstablecimientosCreateForm($establecimientos);

        //Añadir nuevo Subestablecimiento
        $subestablecimientos = new Subestablecimientos();
        $subestab = $this->createSubestablecimientosCreateForm($subestablecimientos);

        return $this->render('ScalofrioBundle:User:clienteList.html.twig', array('pagination' => $pagination,
                            'estab' => $estab->createView(), 'subestab' => $subestab->createView(), 'user' => $usuario));
    }

    public function busquedaUserAction(Request $request)
    {

        $busqueda = trim($_POST['buscar']);
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuarios::class)->findOneBy(array('id' => $this->getUser()->getId()));

        $dql = "SELECT u FROM ScalofrioBundle:Usuarios u
        WHERE u.username LIKE '%" . $busqueda . "%'
        ORDER BY u.username";
        $prod = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(

            $prod,
            $request->query->getInt('page', 1),
            10

        );

        return $this->render('ScalofrioBundle:User:userList.html.twig', array('pagination' => $pagination, 'user' => $usuario));
    }

    //FUNCIONES PARA OBTENER ELEMENTOS EN SELECTS DEPENDIENTES
    public function obtenerEstablecimientosAction($idcliente)
    {

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT e FROM ScalofrioBundle:Establecimientos e
        WHERE e.cliente = '" . $idcliente . "'";
        $query = $em->createQuery($dql);
        $estab = $query->getResult();

        $select = '<option></option>';
        foreach ($estab as $est) {
            $select .= '<option value="' . $est->getId() . '">' . $est->getNombre() . '</option>';
        }
        return new Response($select);
    }

    public function obtenerSubestablecimientosAction($idestablecimiento)
    {

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT e FROM ScalofrioBundle:Subestablecimientos e
        WHERE e.establecimientos = '" . $idestablecimiento . "'";
        $query = $em->createQuery($dql);
        $subestab = $query->getResult();

        $select = '<option></option>';
        foreach ($subestab as $s) {
            $select .= '<option value="' . $s->getId() . '">' . $s->getNombre() . '</option>';
        }
        return new Response($select);
    }

    public function obtenerRepuestosAction($idmaquina)
    {

        $em = $this->getDoctrine()->getManager();
        $repuestos = $em->getRepository('ScalofrioBundle:Repuestos')->findBy(
            array(
                'maquinas' => $idmaquina,
            )
        );

        $option = '';
        foreach ($repuestos as $r) {
            $option .= '<option value="' . $r->getId() . '">' . $r->getNombre() . '</option>';
        }
        return new Response($option);
    }

    public function seleccionIncidenciaAction($idincidencia)
    {

        $em = $this->getDoctrine()->getManager();
        $incidenciaCliente = $em->getRepository('ScalofrioBundle:IncidenciasCliente')->find($idincidencia);
        $cliente = $incidenciaCliente->getUsuario()->getCliente()->getId();
        $estab = 0;$bar = 0;
        if($incidenciaCliente->getEstablecimientos() != null){
            $estab = $incidenciaCliente->getEstablecimientos()->getId();
        }
        if($incidenciaCliente->getSubestablecimientos() != null){
            $bar = $incidenciaCliente->getSubestablecimientos()->getId();
        }

        $resultado = array(
            'cliente' => $cliente,
            'establecimiento' => $estab,
            'bar' => $bar
        );

        return new JsonResponse($resultado);
    }

}
