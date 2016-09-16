<?php

namespace Darkbluesun\GoldfishBundle\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Darkbluesun\GoldfishBundle\Entity\Task;
use Darkbluesun\GoldfishBundle\Entity\TimeEntry;

class TaskController extends AbstractController
{
    public function cgetAction()
    {
        $tasks = $this->getUser()->getWorkspace()->getTasks()->toArray();
        return $this->restResponse($tasks, ['task_list']);
    }

    /**
     * @Security("is_granted('VIEW', task)")
     */
    public function getAction(Task $task)
    {
        return $this->restResponse($task, ['task_details']);
    }

    public function postAction(Request $request)
    {
        $task = $this->serializer->deserialize($request->getContent(), Task::class, 'json');
        $task->setWorkspace($this->getUser()->getWorkspace());
        $task->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime());
        $task = $this->em->merge($task);
        $this->em->flush();

        $this->newObjectPermission($task);

        return $this->getAction($task);
    }

    /**
     * @Security("is_granted('EDIT', task)")
     */
    public function putAction(Request $request, Task $task)
    {
        $created = $task->getCreatedAt();
        $task = $this->serializer->deserialize($request->getContent(), Task::class, 'json');
        $task->setCreatedAt($created)->setUpdatedAt(new \DateTime());
        $task = $this->em->merge($task);
        $this->em->flush();

        return $this->getAction($task);
    }

    /**
     * @Security("is_granted('DELETE', task)")
     */
    public function deleteAction(Request $request, Task $task)
    {
        $this->em->remove($task);
        $this->em->flush();
    }

    /**
     * @Security("is_granted('VIEW', task)")
     */
    public function getCommentsAction(Task $task)
    {
        return $this->restResponse($task->getComments(), ['comments_list']);
    }

    /**
     * @Security("is_granted('VIEW', task)")
     */
    public function getTimesheetAction(Task $task)
    {
        return $this->restResponse($task->getTimeEntries(), ['timesheet_list']);
    }

    /**
     * @Security("is_granted('EDIT', task)")
     */
    public function postTimeAction(Request $request, Task $task)
    {
        $entry = $this->serializer->deserialize($request->getContent(), TimeEntry::class, 'json');
        $entry->setTask($task);
        $entry->setUser($this->get('security.context')->getToken()->getUser());
        $this->em->persist($entry);
        $this->em->flush();
        return $this->restResponse($entry, ['task_details']);
    }
}
