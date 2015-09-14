<?php

namespace Darkbluesun\GoldfishBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Darkbluesun\GoldfishBundle\Entity\Project;
use Darkbluesun\GoldfishBundle\Entity\Task;
use Darkbluesun\GoldfishBundle\Form\ProjectType;

/**
 * Project controller.
 *
 * @Route("/api/projects")
 */
class ProjectController extends Controller
{

    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $workspace = $user->getWorkspace();
        $entities = $workspace->getProjects();
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $entity->__toArray();
        }

        return new JsonResponse($data);
    }
    /**
     * Lists all Project entities.
     *
     * @Route("/list", name="project_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DarkbluesunGoldfishBundle:Project')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Project entity.
     *
     * @Route("/", name="project_create")
     * @Method("POST")
     * @Template("DarkbluesunGoldfishBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Project();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->get('security.context')->getToken()->getUser();
            $entity->setWorkspace($user->getWorkspace());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('project_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create','attr'=>['class'=>'btn btn-default btn-lg']));

        return $form;
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new Project();
        $client_id = $request->query->get('client_id');
        if (!empty($client_id)) {
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
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{id}", name="project_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DarkbluesunGoldfishBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
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
     * Lists all Tasks belonging to this thing.
     *
     * @Route("/{id}/tasks", name="project_task_list")
     * @Method("GET")
     * @Template()
     */
    public function tasksAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository('DarkbluesunGoldfishBundle:Project')->find($id);

        $tasks = $project->getTasks();

        return array(
            'project'=>$project,
            'tasks' => $tasks,
        );
    }

    /**
     * Task quick add
     *
     * @Route("/{id}/addtask/", name="project_quick_task")
     * @Method("POST")
     * @Template()
     */
    public function addTimeAction(Request $request, Project $project) {
        $em = $this->getDoctrine()->getManager();
        $task = new Task();
        $task->setProject($project);
        $task->setName($request->request->get('name'));
        $task->setDue(new \DateTime($request->request->get('due')));
        $task->setTime($request->request->get('time'));
        $em->persist($task);
        $em->flush();
        return new JsonResponse(['success']);
    }

    /**
     * Lists all Comments belonging to this thing.
     *
     * @Route("/{id}/comments", name="project_comments_list")
     * @Method("GET")
     * @Template()
     */
    public function commentsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository('DarkbluesunGoldfishBundle:Project')->find($id);

        $comments = $project->getComments();

        return array(
            'comments' => $comments,
        );
    }

    /**
    * Creates a form to edit a Project entity.
    *
    * @param Project $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('project_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Project entity.
     *
     * @Route("/{id}", name="project_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DarkbluesunGoldfishBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return new JsonResponse(['success'=>true]);
            return $this->redirect($this->generateUrl('project_edit', array('id' => $id)));
        }

        return new JsonResponse(['success'=>false]);
    }
    /**
     * Deletes a Project entity.
     *
     * @Route("/{id}", name="project_delete")
     * @Method("DELETE")
     */
    public function destroyAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DarkbluesunGoldfishBundle:Project')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Project entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project'));
    }

    /**
     * Prepare to delete this thing.
     *
     * @Route("/{id}/delete", name="project_delete_confirm")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction(Project $project)
    {
        $deleteForm = $this->createDeleteForm($project->getId());
        return ['project'=>$project, 'delete_form'=>$deleteForm->createView()];
    }

    /**
     * Creates a form to delete a Project entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
