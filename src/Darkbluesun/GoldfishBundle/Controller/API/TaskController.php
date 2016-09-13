<?php

namespace Darkbluesun\GoldfishBundle\Controller\API;

use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Darkbluesun\GoldfishBundle\Entity\Task;
use Darkbluesun\GoldfishBundle\Entity\TimeEntry;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class TaskController extends Controller implements ClassResourceInterface
{
    public function cgetAction()
    {
        return new Response(
            $this->get('serializer')->serialize(
                array_values($this->getUser()->getWorkspace()->getTasks()->toArray()), 'json',
                SerializationContext::create()->setGroups(['task_list'])
            ));
    }

    /**
     * @Security("is_granted('VIEW', task)")
     */
    public function getAction(Task $task)
    {
        return new Response(
            $this->get('serializer')->serialize(
                $task, 'json',
                SerializationContext::create()->setGroups(['task_details'])
            ));
    }

    public function postAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $this->get('serializer')->deserialize($request->getContent(), Task::class, 'json');
        $task->setWorkspace($this->getUser()->getWorkspace());
        $task->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime());
        $task = $em->merge($task);
        $em->flush();

        $aclProvider = $this->get('security.acl.provider');
        $acl = $aclProvider->createAcl(ObjectIdentity::fromDomainObject($task));
        $acl->insertObjectAce(UserSecurityIdentity::fromAccount($this->getUser()), MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);

        return $this->getAction($task);
    }

    /**
     * @Security("is_granted('EDIT', task)")
     */
    public function putAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $created = $task->getCreatedAt();
        $task = $this->get('serializer')->deserialize($request->getContent(), Task::class, 'json');
        $task->setCreatedAt($created)->setUpdatedAt(new \DateTime());
        $task = $em->merge($task);
        $em->flush();

        return $this->getAction($task);
    }

    /**
     * @Security("is_granted('DELETE', task)")
     */
    public function deleteAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();
        return new JsonResponse(['success'=>true]);
    }

    /**
     * @Security("is_granted('VIEW', task)")
     */
    public function getCommentsAction(Task $task)
    {
        return new Response(
            $this->get('serializer')->serialize(
                $task->getComments(), 'json',
                SerializationContext::create()->setGroups(['comments_list'])
            ));
    }

    /**
     * @Security("is_granted('VIEW', task)")
     */
    public function getTimesheetAction(Task $task) {
        return new Response(
            $this->get('serializer')->serialize(
                $task->getTimeEntries(), 'json',
                SerializationContext::create()->setGroups(['timesheet_list'])
            ));
    }

    /**
     * @Security("is_granted('EDIT', task)")
     */
    public function postTimeAction(Request $request, Task $task) {
        $em = $this->getDoctrine()->getManager();
        $entry = $this->get('serializer')->deserialize($request->getContent(), TimeEntry::class, 'json');
        $entry->setTask($task);
        $entry->setUser($this->get('security.context')->getToken()->getUser());
        $em->persist($entry);
        $em->flush();
        return new Response(
            $this->get('serializer')->serialize(
                $entry, 'json',
                SerializationContext::create()->setGroups(['task_details '])
            ));
    }
}
