<?php

namespace ScalofrioBundle\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
class IncidenciasClientesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                    'fechaIncidencia', 
                    DateType::class, array(
                        'widget'=> 'single_text',
                        'required' => true,
                        'attr' => [
                            'class' => 'form-control input-inline datetimepicker js-datepicker',
                            'data-provide' => 'datetimepicker',
                            'html5' => false,
                        ],
                    )
            )
            ->add('titulo')
            ->add('tipo')
            ->add('cliente')
            ->add(
                    'prioridad',
                    'choice', 
                    array(
                        'choices' => array(
                                        'BAJA'=>'BAJA',
                                        'MEDIA'=>'MEDIA', 
                                        'ALTA'=>'ALTA',
                                        'URGENTE'=>'URGENTE'
                                    )
                    )
            )
            ->add('descripcion',TextareaType::class, array(
                'attr' => array('class' => 'tinymce', 'placeholder' => 'Descripción de la incidencia...')))
            ->add(
                'guardar', 
                SubmitType::class, 
                array(
                    'attr' => array('class' => ' btn btn-info'),
                    'label' => 'Guardar'
                )
            )
            ->add(
                'cancelar',
                ButtonType::class,
                array(
                    'attr' => array('class' => 'btn btn-dark'),
                    'label' => 'Cancelar'
                )
            )
            
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
