<?php

namespace Darkbluesun\GoldfishBundle\Controller;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Darkbluesun\GoldfishBundle\Entity\Workspace;
use Darkbluesun\GoldfishBundle\Entity\User;

/**
 * User controller.
 *
 * @Route("/api/users")
 */
class UserController extends Controller
{
    /**
     * Lists all Users in this user's workspace.
     *
     * @Route("/", name="users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
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
