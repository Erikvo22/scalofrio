<?php

namespace ScalofrioBundle\Controller;

use ScalofrioBundle\Entity\Cliente;
use ScalofrioBundle\Entity\Comercial;
use ScalofrioBundle\Entity\Establecimientos;
use ScalofrioBundle\Entity\Gestion;
use ScalofrioBundle\Entity\Incidencias;
use ScalofrioBundle\Entity\Maquinas;
use ScalofrioBundle\Entity\Repuestos;
use ScalofrioBundle\Entity\Usuarios;
use ScalofrioBundle\Form\ClienteType;
use ScalofrioBundle\Form\ComercialType;
use ScalofrioBundle\Form\EstablecimientosType;
use ScalofrioBundle\Form\GestionType;
use ScalofrioBundle\Form\IncidenciasType;
use ScalofrioBundle\Form\MaquinasType;
use ScalofrioBundle\Form\RepuestosType;
use ScalofrioBundle\Form\UsuariosType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function homeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $rol = $this->getUser()->getRoles();
        if ($rol[0] == 'ROLE_ADMIN') {
            $dql = "SELECT u FROM ScalofrioBundle:Incidencias u";
        } else {
            $dql = "SELECT u FROM ScalofrioBundle:IncidenciasCliente u";
        }

        $incidencias = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $incidencias, $request->query->getInt('page', 1),
            10
        );
        if ($rol[0] == 'ROLE_ADMIN') {
            return $this->render('ScalofrioBundle:User:index.html.twig', array('pagination' => $pagination));
        } else {
            return $this->render('ScalofrioBundle:User:historialIncidenciaClientes.html.twig', array('pagination' => $pagination));
        }
    }

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = $this->getUser()->getRoles();

        if ($rol[0] == 'ROLE_ADMIN') {
            $dql = "SELECT u FROM ScalofrioBundle:Incidencias u";
        } else {
            $dql = "SELECT u FROM ScalofrioBundle:IncidenciasCliente u";
        }

        $incidencias = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $incidencias, $request->query->getInt('page', 1),
            10
        );
        if ($rol[0] == 'ROLE_ADMIN') {
            return $this->render('ScalofrioBundle:User:index.html.twig', array('pagination' => $pagination));
        } else {

            return $this->render('ScalofrioBundle:User:historialIncidenciaClientes.html.twig', array('pagination' => $pagination));
        }
    }

    /******** APARTADO DE INCIDENCIAS **********/

    public function incidenciaAddAction()
    {
        $incidencia = new Incidencias();
        $form = $this->createIncidenciaCreateForm($incidencia);

        return $this->render('ScalofrioBundle:User:IncidenciaAdd.html.twig', array('form' => $form->createView()));
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
        $incidencia = new Incidencias();
        $form = $this->createIncidenciaCreateForm($incidencia);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($incidencia);
            $em->flush();

            /*ENVÍO DE EMAIL*/
            try {
                $message = \Swift_Message::newInstance()
                    ->setSubject('INCIDENCIA SCALOF;RIO S.L.')
                    ->setFrom('erikvieraol22@gmail.com')
                    ->setTo('erik.viera@iecisa.com')
                    ->setBody('Prueba');

                $this->get('mailer')->send($message);
            } catch (\Exception $e) {
                throw $e;
            }

            $this->addFlash(
                'mensaje',
                'Incidencia creada correctamente.'
            );

            return $this->redirectToRoute('scalofrio_index');
        }
        return $this->render('ScalofrioBundle:User:IncidenciaAdd.html.twig', array('form' => $form->createView()));
    }

    /*/# EDICIÓN #/*/

    public function incidenciaEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $incidencia = $em->getRepository('ScalofrioBundle:Incidencias')->find($id);

        if (!$incidencia) {
            $messageException = 'Incidencia no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createIncidenciaEditForm($incidencia);

        return $this->render('ScalofrioBundle:User:incidenciaEdit.html.twig', array('incidencia' => $incidencia, 'form' => $form->createView()));
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
        return $this->render('ScalofrioBundle:User:incidenciaEdit.html.twig', array('incidencia' => $incidencia, 'form' => $form->createView()));
    }

    /*VER*/
    public function incidenciaViewAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('ScalofrioBundle:Incidencias');
        $incidencia = $repository->find($id);

        if (!$incidencia) {
            $messageException = 'Incidencia no encontrada.';
            throw $this->createNotFoundException($messageException);
        }

        $deleteForm = $this->createIncidenciaDeleteForm($incidencia);

        return $this->render('@Scalofrio/User/incidenciaView.html.twig', array('incidencia' => $incidencia, 'delete_form' => $deleteForm->createView()));
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
            'Ruta',
            'Nombre cliente',
            'Cargo cliente',
            'Gestion',
            'Resultado',
            'Tiempo(min)',
            'Descripcion',
        ]);

        foreach ($incidencias as $incidencia) {
            $csv->insertOne([
                $incidencia->getId(),
                $incidencia->getFecha()->format('d/m/y'),
                $incidencia->getComercial()->getNombre(),
                $incidencia->getCliente()->getNombre(),
                $incidencia->getEstablecimiento(),
                $incidencia->getRuta()->getNombre(),
                $incidencia->getNombrecliente(),
                $incidencia->getCargocliente(),
                $incidencia->getGestion(),
                $incidencia->getResultado(),
                $incidencia->getTiempo(),
                $incidencia->getRepuestos(),
            ]);
        }
        $csv->output('incidencias.csv');
        die;
    }

    /******** APARTADO DE USUARIOS **********/

    public function userListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT u FROM ScalofrioBundle:Usuarios u";
        $usuarios = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $usuarios, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:userList.html.twig', array('pagination' => $pagination));
    }

    public function userAddAction()
    {
        $user = new Usuarios();
        $form = $this->createUserCreateForm($user);

        return $this->render('ScalofrioBundle:User:userAdd.html.twig', array('form' => $form->createView()));
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
        $user = new Usuarios();
        $form = $this->createUserCreateForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $password = $form->get('password')->getData();
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Usuario creado correctamente'
            );

            return $this->redirectToRoute('scalofrio_user_list');
        }
        return $this->render('ScalofrioBundle:User:userAdd.html.twig', array('form' => $form->createView()));
    }

    public function userEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ScalofrioBundle:Usuarios')->find($id);

        if (!$user) {
            $messageException = 'Usuario no encontrado.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createUserEditForm($user);

        return $this->render('ScalofrioBundle:User:userEdit.html.twig', array('user' => $user
            , 'form' => $form->createView()));
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
        $user = $em->getRepository('ScalofrioBundle:Usuarios')->find($id);

        if (!$user) {
            $messageException = 'Usuario no encontrado.';
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createUserEditForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $successMessage = 'Usuario actualizado correctamente';
            $this->addFlash('mensaje', $successMessage);
            return $this->redirectToRoute('scalofrio_user_list', array('id' => $user->getId()));
        }
        return $this->render('ScalofrioBundle:User:userEdit.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    /******** APARTADO DE COMERCIALES **********/

    public function comercialAddAction()
    {
        $comercial = new Comercial();
        $form = $this->createComercialCreateForm($comercial);

        return $this->render('ScalofrioBundle:User:comercialAdd.html.twig', array('form' => $form->createView()));
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

            return $this->redirectToRoute('scalofrio_index');
        }
        return $this->render('ScalofrioBundle:User:comercialAdd.html.twig', array('form' => $form->createView()));
    }

    /******** APARTADO DE CLIENTES **********/

    public function clienteListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT u FROM ScalofrioBundle:Cliente u";
        $clientes = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $clientes, $request->query->getInt('page', 1),
            10
        );

        //Añadir nuevo establecimiento
        $establecimientos = new Establecimientos();
        $form = $this->createEstablecimientosCreateForm($establecimientos);

        return $this->render('ScalofrioBundle:User:clienteList.html.twig', array('pagination' => $pagination, 'form' => $form->createView()));
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

        if (!$cliente) {
            $messageException = 'Cliente no encontrado.';
            throw $this->createNotFoundException($messageException);
        }

        return $this->render('@Scalofrio/User/clienteView.html.twig', array('cliente' => $cliente, 'establecimientos' => $establecimientos));
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

        $dql = "SELECT u FROM ScalofrioBundle:Maquinas u";
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

    public function gestionAddAction()
    {
        $gestion = new Gestion();
        $form = $this->createGestionCreateForm($gestion);

        return $this->render('ScalofrioBundle:User:gestionAdd.html.twig', array('form' => $form->createView()));
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

            return $this->redirectToRoute('scalofrio_index');
        }
        return $this->render('ScalofrioBundle:User:gestionAdd.html.twig', array('form' => $form->createView()));
    }

    /******** BÚSQUEDA **********/

    public function busquedaAction(Request $request)
    {

        $busqueda = trim($_POST['buscar']);

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT i FROM ScalofrioBundle:Incidencias i
        JOIN i.cliente c
        WHERE c.nombre LIKE '%" . $busqueda . "%'
        OR i.establecimiento LIKE '%" . $busqueda . "%'
        ORDER BY i.fecha";
        $prod = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(

            $prod,
            $request->query->getInt('page', 1),
            10

        );
        return $this->render('ScalofrioBundle:User:index.html.twig', array('pagination' => $pagination));
    }

    public function busquedaClienteAction(Request $request)
    {

        $busqueda = trim($_POST['buscar']);

        $em = $this->getDoctrine()->getManager();

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
        $form = $this->createEstablecimientosCreateForm($establecimientos);

        return $this->render('ScalofrioBundle:User:clienteList.html.twig', array('pagination' => $pagination, 'form' => $form->createView()));
    }

    public function busquedaUserAction(Request $request)
    {

        $busqueda = trim($_POST['buscar']);

        $em = $this->getDoctrine()->getManager();

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

        return $this->render('ScalofrioBundle:User:userList.html.twig', array('pagination' => $pagination));
    }

    //FUNCIONES PARA OBTENER ELEMENTOS EN SELECTS DEPENDIENTES
    public function obtenerEstablecimientosAction($idcliente){

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT e FROM ScalofrioBundle:Establecimientos e
        WHERE e.cliente = '" . $idcliente . "'";
        $query = $em->createQuery($dql);
        $estab = $query->getResult();

        $select = '';
        foreach ($estab as $est){
            $select .= '<option value="'.$est->getId().'">'.$est->getNombre().'</option>';
        }
        return new Response($select);
    }

}
