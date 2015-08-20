<?php

namespace Darkbluesun\GoldfishBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Darkbluesun\GoldfishBundle\Entity\Task;
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
        $user = $this->get('security.context')->getToken()->getUser();
        $workspace = $user->getWorkspace();
        $entities = $workspace->getTasks();
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $entity->__toArray();
        }

        return new JsonResponse($data);
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/", name="tasks_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new Task();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->get('security.context')->getToken()->getUser();
            $entity->setWorkspace($user->getWorkspace());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return new JsonResponse($entity);
        }

    }

    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}", name="tasks_update")
     * @Method("POST")
     */
    public function updateAction(Task $todo)
    {
        $em = $this->getDoctrine()->getManager();
        $postData = json_decode($this->get("request")->getContent());
        $todo->setDone($postData->done);
        $em->flush();
        return new JsonResponse($todo->__toArray());
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}", name="tasks_delete")
     * @Method("DELETE")
     */
    public function destroyAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DarkbluesunGoldfishBundle:Task')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Task entity.');
            }

            $em->remove($entity);
            $em->flush();

            return new JsonResponse(['success'=>true]);
        }

    }

    /**
     * Lists all Comments belonging to this thing.
     *
     * @Route("/{id}/comments", name="task_comment_list")
     * @Method("GET")
     */
    public function commentsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository('DarkbluesunGoldfishBundle:Task')->find($id);

        $comments = $task->getComments();

        return new JsonResponse(array(
            'comments' => $comments,
        ));
    }

    /**
     * List all time entries
     *
     * @Route("/{id}/timesheet/", name="task_timesheet")
     * @Method("GET")
     * @Template()
     */
    public function timesheetAction(Task $task) {
        $timesheet = $task->getTimeEntries();
        return new JsonResponse([ 'task'=>$task, 'timesheet'=>$timesheet ]);
    }

    /**
     * Time add
     *
     * @Route("/{id}/addtime/", name="task_add_time")
     * @Method("POST")
     */
    public function addTimeAction(Request $request, Task $task) {
        $em = $this->getDoctrine()->getManager();
        $entry = new TimeEntry();
        $entry->setStart(new \DateTime($request->request->get('start-time')));
        $entry->setEnd(new \DateTime($request->request->get('end-time')));
        $entry->setComment($request->request->get('description'));
        $entry->setTask($task);
        $entry->setUser($this->get('security.context')->getToken()->getUser());
        $em->persist($entry);
        $em->flush();
        return new JsonResponse(['success']);
    }
}
