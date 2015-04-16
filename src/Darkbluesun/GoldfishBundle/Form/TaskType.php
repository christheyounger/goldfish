<?php

namespace Darkbluesun\GoldfishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client',null,['required'=>false])
            ->add('project',null,['required'=>false])
            ->add('assignee',null,['required'=>false])
            ->add('time',null,['label'=>'Budgeted hours'])
            ->add('name')
            ->add('due','datetime',array('widget'=>'single_text','format'=>'dd/MM/yyyy HH:mm','attr'=>array('class'=>'datetimepicker')))
            ->add('description')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Darkbluesun\GoldfishBundle\Entity\Task'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'darkbluesun_goldfishbundle_task';
    }
}
