<?php
namespace Darkbluesun\GoldfishBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserType());
        $builder->add(
            'terms',
            'checkbox',
            array('label'=>'I accept the terms and conditions','property_path' => 'termsAccepted')
        );
        $builder->add('Register', 'submit',['attr'=>['class'=>'btn btn-lg btn-primary btn-block ']]);
    }

    public function getName()
    {
        return 'registration';
    }
}