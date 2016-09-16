<?php

namespace Darkbluesun\GoldfishBundle\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Darkbluesun\GoldfishBundle\Entity\Workspace;
use Darkbluesun\GoldfishBundle\Entity\Client;
use Darkbluesun\GoldfishBundle\Entity\ClientComment;

class ClientController extends AbstractController
{
    public function cgetAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $workspace = $user->getWorkspace();

        if (!$workspace) {
            $workspace = new Workspace();
            $workspace->addUser($user);
            $workspace->setPrivate(true);
            $workspace->setName($user->getEmail()."'s workspace");
            $this->em->persist($workspace);
            $this->em->flush();
        }

        return $this->restResponse($workspace->getClients(), ['client_list']);
    }

    /**
     * @Security("is_granted('VIEW', client)")
     */
    public function getAction(Client $client)
    {
        return $this->restResponse($client, ['client_details']);
    }

    public function postAction(Request $request)
    {
        $client = $this->serializer->deserialize($request->getContent(), Client::class, 'json');
        $client->setWorkspace($this->getUser()->getWorkspace());
        $client->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime());
        $this->em->persist($client);
        $this->em->flush();

        $this->newObjectPermission($client);

        return $this->getAction($client);
    }

    /**
     * @Security("is_granted('EDIT', client)")
     */
    public function putAction(Request $request, Client $client)
    {
        $created = $client->getCreatedAt();
        $client = $this->serializer->deserialize($request->getContent(), Client::class, 'json');
        $client->setCreatedAt($created)->setUpdatedAt(new \DateTime());
        $this->em->merge($client);
        $this->em->flush();
        return $this->getAction($client);
    }

    public function postCommentAction(Request $request, Client $client)
    {
        $comment = new ClientComment;
        $comment->setClient($client);
        $comment->setContent($request->request->get('content'));
        $this->em->persist($comment);
        $this->em->flush();

        return $this->restResponse($client, ['client_details']);
    }

    /**
     * @Security("is_granted('DELETE', client)")
     */
    public function deleteAction(Request $request, Client $client)
    {
        $this->em->remove($client);
        $this->em->flush();
    }

}
