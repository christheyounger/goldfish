<?php
namespace Darkbluesun\GoldfishBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{    
    public function buildUserForm(FormBuilderInterface $builder, array $options)
    {        
        // custom field       
        $builder->add('firstName');
        $builder->add('lastName');
    }

    public function getParent()
    {
        return 'fos_user_profile';
    }

    public function getName()
    {
        return 'goldfish_user_profile';
    }
}