<?php

namespace ScalofrioBundle\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\CallbackTransformer;

class IncidenciasClientesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaIncidencia', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control input-inline datetimepicker',
                    'data-provide' => 'datetimepicker',
                    'html5' => false,
                ],
            ))
            ->add('titulo')
            ->add('tipo')
            ->add('prioridad','choice', array('choices' => array(''=>'','RESUELTO'=>'RESUELTO', 'NO RESUELTO'=>'NO RESUELTO')))
            ->add('descripcion',TextareaType::class, array(
                'attr' => array('class' => 'tinymce', 'placeholder' => 'Descripción de la incidencia...')))
            ->add('guardar', 'submit', array('label' => 'Guardar'))
        ;
     

        //Esto es para que el campo fecha tenga por defecto el valor de hoy.
        $builder->get('fechaIncidencia')->addModelTransformer(new CallbackTransformer(
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
            'data_class' => 'ScalofrioBundle\Entity\IncidenciasCliente'
        ));
    }

}
