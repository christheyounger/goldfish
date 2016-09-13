<?php

namespace Darkbluesun\GoldfishBundle\Controller\API;

use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Darkbluesun\GoldfishBundle\Entity\Workspace;
use Darkbluesun\GoldfishBundle\Entity\Client;
use Darkbluesun\GoldfishBundle\Entity\ClientComment;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class ClientController extends Controller implements ClassResourceInterface
{
    public function cgetAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $workspace = $user->getWorkspace();

        if (!$workspace) {
            $em = $this->getDoctrine()->getManager();
            $workspace = new Workspace();
            $workspace->addUser($user);
            $workspace->setPrivate(true);
            $workspace->setName($user->getEmail()."'s workspace");
            $em->persist($workspace);
            $em->flush();
        }

        return new Response(
            $this->get('serializer')->serialize(
                    $workspace->getClients(), 'json',
                    SerializationContext::create()->setGroups(['client_list'])
            ));
    }

    /**
     * @Security("is_granted('VIEW', client)")
     */
    public function getAction(Client $client)
    {
        return new Response($this->get('serializer')->serialize($client,'json',SerializationContext::create()->setGroups(['client_details'])));
    }

    public function postAction(Request $request)
    {
        $serializer = $this->get('serializer');
        $em = $this->getDoctrine()->getManager();
        $client = $serializer->deserialize($request->getContent(), Client::class, 'json');
        $client->setWorkspace($this->getUser()->getWorkspace());
        $client->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime());
        $em->persist($client);
        $em->flush();

        $aclProvider = $this->get('security.acl.provider');
        $acl = $aclProvider->createAcl(ObjectIdentity::fromDomainObject($client));
        $acl->insertObjectAce(UserSecurityIdentity::fromAccount($this->getUser()), MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);

        return $this->getAction($client);
    }

    /**
     * @Security("is_granted('EDIT', client)")
     */
    public function putAction(Request $request, Client $client)
    {
        $em = $this->getDoctrine()->getManager();
        $created = $client->getCreatedAt();
        $client = $this->get('serializer')->deserialize($request->getContent(), Client::class, 'json');
        $client->setCreatedAt($created)->setUpdatedAt(new \DateTime());
        $em->merge($client);
        $em->flush();
        return $this->getAction($client);
    }

    public function postCommentAction(Request $request, Client $client) {
        $comment = new ClientComment;
        $em = $this->getDoctrine()->getManager();

        $comment->setClient($client);
        $comment->setContent($request->request->get('content'));
        $em->persist($comment);
        $em->flush();

        return new Response($this->get('serializer')->serialize($client,'json',SerializationContext::create()->setGroups(['client_details'])));
    }

    /**
     * @Security("is_granted('DELETE', client)")
     */
    public function deleteAction(Request $request, Client $client)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($client);
        $em->flush();
        return new Response(null, Response::HTTP_OK);
    }

}
