<?php

namespace Darkbluesun\GoldfishBundle\Controller;

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
                $this->getUser()->getWorkspace()->getTasks(),
                'json',['groups'=>['task_list']]
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
        return new Response($this->get('serializer')->serialize($task,'json',['groups'=>['task_details']]));
    }

    /**
     * Creates a new Task.
     *
     * @Route("", name="tasks_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $task->setWorkspace($this->getUser()->getWorkspace());
        $this->applyData($task,(array)json_decode($request->getContent()));

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
        $this->applyData($task,(array)json_decode($request->getContent()));
        return $this->getAction($task);
    }

    private function applyData(Task $task, Array $data) {
        $em = $this->getDoctrine()->getManager();
        $keys = ['name','done','due','client','project','assignee','time','description'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                // Transform
                switch ($key) {
                    case 'due': try { $data[$key] = new \DateTime($data[$key]); } catch (\Exception $e) { continue; } break;
                    case 'client': $data[$key] = $em->find(Client::class,$data[$key]->id); break;
                    case 'project': $data[$key] = $em->find(Project::class,$data[$key]->id); break;
                    case 'assignee': $data[$key] = $em->find(User::class,$data[$key]->id); break;
                }
                // Set
                $setter = 'set'.ucfirst($key);
                $task->$setter($data[$key]);
            }
        }
        if (!$task->getId()) $em->persist($task);
        $em->flush();
        return true;
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
                $task->getComments(),
                'json',['groups'=>['comments_list']]
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
                $task->getTimeEntries(),
                'json',['groups'=>['timesheet_list']]
            ));
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
        return new Response($this->get('serializer')->serialize($entry,'json',['groups'=>['time_details']]));
    }
}
