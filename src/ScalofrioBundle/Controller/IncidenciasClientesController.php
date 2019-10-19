<?php
namespace ScalofrioBundle\Controller;

use ScalofrioBundle\Entity\IncidenciasCliente;
use ScalofrioBundle\Form\IncidenciasClientesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IncidenciasClientesController extends Controller
{
    public function newIncidenciaClienteAction(Request $request)
    {

        $incidencia = new IncidenciasCliente();

        $form = $this->createIncidenciaCreateForm($incidencia);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($incidencia);
            $em->flush();

            $this->addFlash(
                'mensaje',
                'Incidencia creada correctamente.'
            );

            return $this->redirectToRoute('scalofrio_index');
        }

        return $this->render('ScalofrioBundle:User:incidenciaClienteAdd.html.twig', array('form' => $form->createView()));
    }

    public function createIncidenciaCreateForm(IncidenciasCliente $entity)
    {
        $form = $this->createForm(new IncidenciasClientesType(), $entity, array(
            'action' => $this->generateUrl('scalofrio_incidenciaCliente_create'),
            'method' => 'POST',
        ));
        
        return $form;
    }
}
