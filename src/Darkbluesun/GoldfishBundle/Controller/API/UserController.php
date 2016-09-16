<?php

namespace Darkbluesun\GoldfishBundle\Controller\API;

use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller implements ClassResourceInterface
{
    public function cgetAction()
    {
        $user = $this->getUser();
        $workspace = $user->getWorkspace();

        return new Response(
            $this->get('serializer')->serialize(
                    $workspace->getUsers(), 'json',
                    SerializationContext::create()->setGroups(['user_list'])
            ));
    }

}
