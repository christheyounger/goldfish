<?php

namespace Darkbluesun\GoldfishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DarkbluesunGoldfishBundle:Default:index.html.twig', array('name' => 'borris'));
    }
}
