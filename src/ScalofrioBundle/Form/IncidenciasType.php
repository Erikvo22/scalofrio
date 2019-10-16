<?php

namespace ScalofrioBundle\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\CallbackTransformer;

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
            ->add('cargocliente')
            ->add('nombrecliente')
            ->add('firma')
            ->add('resultado','choice', array('choices' => array(''=>'','RESUELTO'=>'RESUELTO', 'NO RESUELTO'=>'NO RESUELTO')))
            ->add('repuestos',TextareaType::class, array(
                'attr' => array('class' => 'tinymce', 'placeholder' => 'Descripción de la incidencia...')))
            ->add('ruta')
            ->add('comercial')
            ->add('cliente')
            ->add('establecimiento','text', array('required' => false))
            ->add('gestion')
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
