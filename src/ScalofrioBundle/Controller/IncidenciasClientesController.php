<?php
namespace ScalofrioBundle\Controller;

use ScalofrioBundle\Entity\IncidenciasCliente;
use ScalofrioBundle\Form\IncidenciasClientesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IncidenciasClientesController extends Controller
{
    public function incidenciaClienteNewAction()
    {
        $incidencia = new IncidenciasCliente();
        $form = $this->createIncidenciaCreateForm($incidencia);
        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('form' => $form->createView(), "modo" => "nuevo"));
    }

    public function incidenciaClienteCreateAction(Request $request)
    {
        $incidencia = new IncidenciasCliente();
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
            $em->persist($incidencia);
            $em->flush();
            $this->sendMail($parameters);
            $this->addFlash(
                'mensaje',
                'Incidencia creada correctamente.'
            );

            return $this->redirectToRoute('scalofrio_index');
        }
        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('form' => $form->createView(), "modo" => "nuevo"));
    }

    public function incidenciaClienteShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $incidencia = $em->getRepository(IncidenciasCliente::class)->findOneBy(array('id' => $id));
        $form = $this->mostrarIncidenciaForm($incidencia);
        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('form' => $form->createView(), "modo" => "lectura"));
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
                'Se ha producido un errror al actualizar el estado.'
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
    private function sendMail($parameters)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('lcs.arjones@gmail.com')
            ->setTo('lcs.arjones@gmail.com')
            ->setBody(
                $this->renderView(
                    'ScalofrioBundle:Email:registrarIncidencia.html.twig',
                    $parameters
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);

    }
    public function listarAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM ScalofrioBundle:IncidenciasCliente u";

        $incidencias = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $incidencias, $request->query->getInt('page', 1),
            10
        );

        return $this->render('ScalofrioBundle:User:historialIncidenciaClientes.html.twig', array('pagination' => $pagination));

    }

}
