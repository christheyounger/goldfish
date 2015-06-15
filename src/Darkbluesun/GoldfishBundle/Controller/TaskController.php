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
 * @Route("/tasks")
 */
class TaskController extends Controller
{

    /**
     * Lists all Task entities.
     *
     * @Route("/", name="tasks")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * Lists all Task entities.
     *
     * @Route("/list", name="tasks_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $workspace = $user->getWorkspace();
        $entities = $workspace->getTasks();
        $data = [];
        foreach ($entities as $entity) {
            $data[] = [
                        'id' => $entity->getId(),
                        'url' => $this->generateUrl('tasks_edit',['id'=>$entity->getId()]),
                        'client' => (String)$entity->getClient(),
                        'project' => (String)$entity->getProject(),
                        'name' => $entity->getName(),
                        'due' => [
                            'sort' => $entity->getDue()->format('YmdHis'),
                            'string' => $entity->getDue()->format('d/m/y ha')
                        ],
                      ];
        }

        return new JsonResponse(['data'=>$data]);
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/", name="tasks_create")
     * @Method("POST")
     * @Template("DarkbluesunGoldfishBundle:Task:new.html.twig")
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

            return $this->redirect($this->generateUrl('tasks_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Task $entity)
    {
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('tasks_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/new", name="tasks_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new Task();
        $project_id = $request->query->get('project_id');
        $client_id = $request->query->get('client_id');
        if (!empty($project_id)) {
            $em = $this->getDoctrine()->getManager();
            $project = $em->getRepository('DarkbluesunGoldfishBundle:Project')->find($project_id);
            if ($project) {
                $entity->setProject($project);
                if ($client = $project->getClient()) {
                    $entity->setClient($client);
                }
            }
        } else if (!empty($client_id)) {
            $em = $this->getDoctrine()->getManager();
            $client = $em->getRepository('DarkbluesunGoldfishBundle:Client')->find($client_id);
            if ($client) {
                $entity->setClient($client);
            }
        }
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}", name="tasks_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DarkbluesunGoldfishBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Lists all Comments belonging to this thing.
     *
     * @Route("/{id}/comments", name="task_comment_list")
     * @Method("GET")
     * @Template()
     */
    public function commentsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository('DarkbluesunGoldfishBundle:Task')->find($id);

        $comments = $task->getComments();

        return array(
            'comments' => $comments,
        );
    }

    /**
    * Creates a form to edit a Task entity.
    *
    * @param Task $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Task $entity)
    {
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('tasks_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}", name="tasks_update")
     * @Method("PUT")
     * @Template("DarkbluesunGoldfishBundle:Task:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DarkbluesunGoldfishBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return new JsonResponse(['success'=>true]);
        }

        return new JsonResponse(['success'=>false]);
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
        return [ 'task'=>$task, 'timesheet'=>$timesheet ];
    }

    /**
     * Timer block
     *
     * @Route("/{id}/timer/", name="task_timer")
     * @Method("GET")
     * @Template()
     */
    public function timerAction(Task $task) {
        return [ 'task'=>$task, ];
    }

    /**
     * Time add
     *
     * @Route("/{id}/addtime/", name="task_add_time")
     * @Method("POST")
     * @Template()
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

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}", name="tasks_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
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
        }

        return $this->redirect($this->generateUrl('tasks'));
    }

    /**
     * Creates a form to delete a Task entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tasks_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
