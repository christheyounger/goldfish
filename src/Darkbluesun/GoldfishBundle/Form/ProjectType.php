<?php

namespace Darkbluesun\GoldfishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client',null,['required'=>false])
            ->add('name')
            ->add('dueDate','datetime',array('widget'=>'single_text','format'=>'dd/MM/yyyy HH:mm','attr'=>array('class'=>'datetimepicker')))
            ->add('budget')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Darkbluesun\GoldfishBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'darkbluesun_goldfishbundle_project';
    }
}
