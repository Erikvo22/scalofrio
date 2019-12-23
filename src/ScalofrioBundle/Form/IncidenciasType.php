<?php

namespace ScalofrioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityRepository;

class IncidenciasType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control input-inline datetimepicker',
                    'data-provide' => 'datetimepicker',
                    'html5' => false,
                ],
            ))
            ->add('tiempo')
            ->add('cargocliente','choice', array('choices' => array(''=>'','JEFE/A DE BARES'=>'JEFE/A DE BARES',
                'MAITRE'=>'MAITRE', 'SEGUNDO/A'=>'SEGUNDO/A', 'JEFE/A SECTOR'=>'JEFE/A SECTOR', 'CAMARERO/A'=>'CAMARERO/A',
                'ECONOMATO' => 'ECONOMATO', 'SSTT CLIENTE' => 'SSTT CLIENTE')))
            ->add('nombrecliente')
            ->add('firma', HiddenType::class)
            ->add('resultado','choice', array('choices' => array(''=>'','RESUELTO'=>'RESUELTO', 'PENDIENTE'=>'PENDIENTE',
                'CAMBIO DE MÁQUINA'=>'CAMBIO DE MÁQUINA')))
            ->add('ruta','choice', array('choices' => array(''=>'','GC NORTE'=>'GC NORTE', 'GC SUR'=>'GC SUR',
                'LANZAROTE'=>'LANZAROTE', 'FUERTEVENTURA'=>'FUERTEVENTURA')))
            ->add('comercial', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Comercial',
                'empty_value' => '',
                'required'    => true
            ))
            ->add('cliente', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Cliente',
                'empty_value' => '',
                'required'    => true
            ))
            ->add('establecimientos', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Establecimientos',
                'empty_value' => '',
                'required'    => false
            ))
            ->add('subestablecimientos', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Subestablecimientos',
                'empty_value' => '',
                'required'    => false
            ))
            ->add('gestion', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Gestion',
                'empty_value' => '',
                'required'    => true
            ))
            ->add('maquinas', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Maquinas',
                'empty_value' => '',
                'required'    => true
            ))
            ->add('repuestos', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Repuestos',
                'multiple' => true,
                'empty_value' => '',
                'required'    => false
            ))
            ->add('email', 'email', array(
                'required'    => false
            ))
            ->add('numinccliente', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\IncidenciasCliente',
                'empty_value' => '',
                'required'    => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.estado = 0')
                        ->orderBy('u.id', 'ASC');
                },
            ))
            ->add('guardar', 'submit', array('label' => 'Guardar'))
        ;

        //Esto es para que el campo fecha tenga por defecto el valor de hoy.
        $builder->get('fecha')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if(!$value) {
                    return new \DateTime('now');
                }
                return $value;
            },
            function ($value) {
                return $value;
            }
        ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ScalofrioBundle\Entity\Incidencias'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'incidencias';
    }
}
