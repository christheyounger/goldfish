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
use Darkbluesun\GoldfishBundle\Entity\Client;
use Darkbluesun\GoldfishBundle\Entity\Project;
use Darkbluesun\GoldfishBundle\Entity\Task;
use Darkbluesun\GoldfishBundle\Entity\User;
use Darkbluesun\GoldfishBundle\Entity\TimeEntry;
use Darkbluesun\GoldfishBundle\Form\TaskType;

/**
 * Task controller.
 *
 * @Route("/api/tasks")
 */
class TaskController extends Controller
{
    /**
     * Lists all Task entities.
     *
     * @Route("/", name="tasks_list")
     * @Method("GET")
     */
    public function listAction()    
    {
        return new Response(
            $this->get('serializer')->serialize(
                array_values($this->getUser()->getWorkspace()->getTasks()->toArray()), 'json',
                SerializationContext::create()->setGroups(['task_list'])
            ));
    }

    /**
     * Gets a Task.
     *
     * @Route("/{id}", name="tasks_get")
     * @Method("GET")
     */
    public function getAction(Task $task)
    {
        return new Response(
            $this->get('serializer')->serialize(
                $task, 'json',
                SerializationContext::create()->setGroups(['task_details'])
            ));
    }

    /**
     * Creates a new Task.
     *
     * @Route("", name="tasks_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $this->get('serializer')->deserialize($request->getContent(), Task::class, 'json');
        $task->setWorkspace($this->getUser()->getWorkspace());
        $task = $em->merge($task);
        $em->flush();

        return $this->getAction($task);
    }

    /**
     * Edits an existing Task.
     *
     * @Route("/{id}", name="tasks_update")
     * @Method("POST")
     */
    public function updateAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $this->get('serializer')->deserialize($request->getContent(), Task::class, 'json');
        $task = $em->merge($task);
        $em->flush();

        return $this->getAction($task);
    }

    /**
     * Deletes a Task.
     *
     * @Route("/{id}", name="tasks_delete")
     * @Method("DELETE")
     */
    public function destroyAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();
        return new JsonResponse(['success'=>true]);
    }

    /**
     * Lists all Comments belonging to this thing.
     *
     * @Route("/{id}/comments", name="task_comment_list")
     * @Method("GET")
     */
    public function commentsAction(Task $task)
    {
        return new Response(
            $this->get('serializer')->serialize(
                $task->getComments(), 'json',
                SerializationContext::create()->setGroups(['comments_list'])
            ));
    }

    /**
     * List all time entries
     *
     * @Route("/{id}/timesheet/", name="task_timesheet")
     * @Method("GET")
     */
    public function timesheetAction(Task $task) {
        return new Response(
            $this->get('serializer')->serialize(
                $task->getTimeEntries(), 'json',
                SerializationContext::create()->setGroups(['timesheet_list'])
            ));
    }

    /**
     * Time add
     *
     * @Route("/{id}/addtime", name="task_add_time")
     * @Method("POST")
     */
    public function addTimeAction(Request $request, Task $task) {
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
