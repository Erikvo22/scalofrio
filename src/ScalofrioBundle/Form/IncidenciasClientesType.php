<?php

namespace ScalofrioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncidenciasClientesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    private $modo = false;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->modo = $options['attr']['modo'] === 'lectura' ? true : false;

        $builder
            ->add('usuario', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Usuarios',
                'empty_value' => '...',
                'required' => true,
                'read_only' => $this->modo,
            ))
            ->add(
                'fechaIncidencia',
                DateType::class, array(
                    'widget' => 'single_text',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control input-inline datetimepicker js-datepicker',
                        'data-provide' => 'datetimepicker',
                        'html5' => false,
                    ],
                    'read_only' => $this->modo,
                )
            )
            ->add('gestion', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Gestion',
                'empty_value' => '...',
                'required'    => true,
                'read_only' => $this->modo,
            ))
            ->add('establecimientos',
                'entity',
                array(
                    'class' => 'ScalofrioBundle\Entity\Establecimientos',
                    'empty_value' => '...',
                    'required'    => false,
                    'read_only' => $this->modo,
                ))
            ->add('subestablecimientos',
                   'entity',
                array(
                'class' => 'ScalofrioBundle\Entity\Subestablecimientos',
                'empty_value' => '...',
                'required'    => false,
                'read_only' => $this->modo,
            ))
            ->add('descripcion', TextareaType::class, array(
                'attr' => array('class' => 'tinymce', 'placeholder' => 'Descripción de la incidencia...'),
                'required'  => true,
                'read_only' => $this->modo,
                )
            )
            ->add(
                'guardar',
                SubmitType::class,
                array(
                    'attr' => array('class' => ' btn btn-info'),
                    'label' => 'Guardar',
                )
            )
            ->add(
                'cancelar',
                ButtonType::class,
                array(
                    'attr' => array('class' => 'btn btn-dark'),
                    'label' => 'Cancelar',
                )
            )

        ;

        //Esto es para que el campo fecha tenga por defecto el valor de hoy.
        $builder->get('fechaIncidencia')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if (!$value) {
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
            'data_class' => 'ScalofrioBundle\Entity\IncidenciasCliente',
        ));
    }

}
