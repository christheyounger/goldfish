<?php

namespace Darkbluesun\GoldfishBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Darkbluesun\GoldfishBundle\Entity\Workspace;
use Darkbluesun\GoldfishBundle\Entity\Client;
use Darkbluesun\GoldfishBundle\Entity\ClientComment;
use Darkbluesun\GoldfishBundle\Form\ClientType;
use Darkbluesun\GoldfishBundle\Form\CommentType;

/**
 * Client controller.
 *
 * @Route("/clients")
 */
class ClientController extends Controller
{

    /**
     * Lists all Client entities.
     *
     * @Route("/", name="clients")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
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
        $entities = $workspace->getClients();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Client entity.
     *
     * @Route("/", name="clients_create")
     * @Method("POST")
     * @Template("DarkbluesunGoldfishBundle:Client:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Client();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->get('security.context')->getToken()->getUser();
            $entity->setWorkspace($user->getWorkspace());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('clients_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Client entity.
     *
     * @param Client $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Client $entity)
    {
        $form = $this->createForm(new ClientType(), $entity, array(
            'action' => $this->generateUrl('clients_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Client entity.
     *
     * @Route("/new", name="clients_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Client();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'edit_form'   => $form->createView(),
        );
    }


    /**
     * Displays a form to edit an existing Client entity.
     *
     * @Route("/{id}", name="clients_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DarkbluesunGoldfishBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $comment = new ClientComment;
        $commentForm = $this->createCommentForm($entity,$comment);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'comment_form' => $commentForm->createView(),
        );
    }

    /**
     * Lists all Projects belonging to this thing.
     *
     * @Route("/{id}/projects", name="client_project_list")
     * @Method("GET")
     * @Template()
     */
    public function projectsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $client = $em->getRepository('DarkbluesunGoldfishBundle:Client')->find($id);

        $projects = $client->getProjects();

        return array(
            'projects' => $projects,
        );
    }

    /**
     * Lists all Comments belonging to this thing.
     *
     * @Route("/{id}/comments", name="client_comment_list")
     * @Method("GET")
     * @Template()
     */
    public function commentsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $client = $em->getRepository('DarkbluesunGoldfishBundle:Client')->find($id);

        $comments = $client->getComments();


        return array(
            'comments' => $comments,
        );
    }

    /**
     * Adds a comment to a client
     *
     * @Route("/{id}/comment", name="clients_comment")
     * @Method("POST")
     */
    public function commentAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('DarkbluesunGoldfishBundle:Client')->find($id);

        if (!$client) {
            throw $this->createNotFoundException('Unable to find Client.');
        }

        $comment = new ClientComment;
        $form = $this->createCommentForm($client,$comment);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $comment->setClient($client);
            $em->persist($comment);
            $em->flush();

            return new JsonResponse(['status'=>'ok']);
        }

        return new JsonResponse(['status'=>'fail','error','form not valid']);
    }

    /**
     * Creates a form to comment on a Client.
     *
     * @param Client $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCommentForm($client,$comment) {

        $form = $this->createForm(new CommentType(), $comment, array(
            'action' => $this->generateUrl('clients_comment',['id'=>$client->getId()]),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Comment'));

        return $form;
    }

    /**
     * Lists all Tasks belonging to this thing.
     *
     * @Route("/{id}/tasks", name="client_task_list")
     * @Method("GET")
     * @Template()
     */
    public function tasksAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $client = $em->getRepository('DarkbluesunGoldfishBundle:Client')->find($id);

        $tasks = $client->getTasks();

        return array(
            'tasks' => $tasks,
        );
    }

    /**
    * Creates a form to edit a Client entity.
    *
    * @param Client $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Client $entity)
    {
        $form = $this->createForm(new ClientType(), $entity, array(
            'action' => $this->generateUrl('clients_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Client entity.
     *
     * @Route("/{id}", name="clients_update")
     * @Method("PUT")
     * @Template("DarkbluesunGoldfishBundle:Client:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DarkbluesunGoldfishBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
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
     * Deletes a Client entity.
     *
     * @Route("/{id}", name="clients_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DarkbluesunGoldfishBundle:Client')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Client entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('clients'));
    }

    /**
     * Creates a form to delete a Client entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clients_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
