<?php

namespace Darkbluesun\GoldfishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('companyName')
            ->add('website',null,['required'=>false])
            ->add('phone',null,['required'=>false])
            ->add('email',null,['required'=>false])
            ->add('contactName',null,['required'=>false])
            ->add('address',null,['required'=>false])
            ->add('description',null,['required'=>false])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Darkbluesun\GoldfishBundle\Entity\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'darkbluesun_goldfishbundle_client';
    }
}
