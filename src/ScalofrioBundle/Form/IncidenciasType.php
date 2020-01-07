<?php

namespace ScalofrioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('cargocliente', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Cargocliente',
                'empty_value' => '',
                'required'    => true
            ))
            ->add('nombrecliente')
            ->add('firma', HiddenType::class)
            ->add('resultado', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Resultados',
                'empty_value' => '',
                'required'    => true
            ))
            ->add('ruta', 'entity', array(
                'class' => 'ScalofrioBundle\Entity\Rutas',
                'empty_value' => '',
                'required'    => true
            ))
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
                        ->orWhere('u.estado is null')
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
