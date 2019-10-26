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
        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('incidencia' => $incidencia, 'form' => $form->createView(), 'modo' => 'nuevo'));
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
                    "data"=> $form->getData()      
                )
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
        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('incidencia' => $incidencia, 'form' => $form->createView(), 'modo' => 'nuevo'));
    }

    public function createIncidenciaCreateForm(IncidenciasCliente $entity)
    {
        $form = $this->createForm(new IncidenciasClientesType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_incidenciaCliente_create'),
            'method' => 'POST',
        ));

        return $form;
    }
    public function incidenciaClienteShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $incidencia = $em->getRepository(IncidenciasCliente::class)->findOneBy(array('id' => $id));
        $form = $this->createIncidenciaEditForm($incidencia);
        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('incidencia' => $incidencia, 'form' => $form->createView(), 'modo' => 'lectura'));
    }

    private function createIncidenciaEditForm(IncidenciasCliente $entity)
    {
        $form = $this->createForm(new IncidenciasClientesType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_incidenciaCliente_editar',
                array('id' => $entity->getId())), 'method' => 'PUT'));
        return $form;
    }

    // public function incidenciaClienteEditAction($id, Request $request)
    // {
    //     $em = $this->getDoctrine()->getManager();
    //     $incidencia = $em->getRepository(IncidenciasCliente::class)->findOneBy(array('id' => $id));

    //     if (!$incidencia) {
    //         $messageException = 'Incidencia no encontrada.';
    //         throw $this->createNotFoundException($messageException);
    //     }

    //     $form = $this->createIncidenciaEditForm($incidencia);
    //     $form->handleRequest($request);
        
    //     return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('incidencia' => $incidencia, 'form' => $form->createView(), 'modo' => 'editar'));
        
        
    //     // if ($form->isSubmitted() && $form->isValid()) {
    //     //     $em->flush();
    //     //     $successMessage = 'Incidencia actualizada correctamente';
    //     //     $this->addFlash('mensaje', $successMessage);
    //     //     return $this->redirectToRoute('scalofrio_index');
    //     // }
    // }

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

}
