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
 * @Route("/api/clients")
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

        $data = [];
        foreach ($entities as $entity) {
            $data[] = $entity->__toArray();
        }

        return new JsonResponse($data);
    }
    /**
     * Creates a new Client entity.
     *
     * @Route("", name="clients_create")
     * @Method("POST")
     * @Template("DarkbluesunGoldfishBundle:Client:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $client = new Client();
        $user = $this->get('security.context')->getToken()->getUser();
        $client->setWorkspace($user->getWorkspace());
        $data = $request->request->all();
        $this->applyData($client,$data);
        $em = $this->getDoctrine()->getManager();
        $em->persist($client);
        $em->flush();
        return new JsonResponse($client->__toArray($client));
    }

    /**
     * Edits an existing Client entity.
     *
     * @Route("/{id}", name="clients_update")
     * @Method("POST")
     * @Template("DarkbluesunGoldfishBundle:Client:edit.html.twig")
     */
    public function updateAction(Request $request, Client $client)
    {
        $em = $this->getDoctrine()->getManager();
        $data = (array)json_decode($request->getContent());
        $this->applyData($client,$data);
        $em->flush();
        return new JsonResponse($client->__toArray($client));
    }

    private function applyData(Client $client, Array $data) {
        $keys = ['companyName','contactName','website','email','phone','address','comments'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $setter = 'set'.ucfirst($key);
                $client->$setter($data[$key]);
            }
        }
    }

    /**
     * Lists all Projects belonging to this thing.
     *
     * @Route("/{id}/projects", name="client_project_list")
     * @Method("GET")
     * @Template()
     */
    public function projectsAction(Client $client)
    {
        $projects = $client->getProjects();

        return array(
            'client'=>$client,
            'projects' => $projects,
        );
    }

    /**
     * Lists all Tasks belonging to this thing.
     *
     * @Route("/{id}/tasks", name="client_task_list")
     * @Method("GET")
     * @Template()
     */
    public function tasksAction(Client $client)
    {
        $tasks = $client->getTasks();

        return array(
            'client'=>$client,
            'tasks' => $tasks,
        );
    }

    /**
     * Lists all Comments belonging to this thing.
     *
     * @Route("/{id}/comments", name="client_comment_list")
     * @Method("GET")
     * @Template()
     */
    public function commentsAction(Client $client)
    {
        $comments = $client->getComments();


        return array(
            'client'=>$client,
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
     * Deletes a Client entity.
     *
     * @Route("/{id}", name="clients_delete")
     * @Method("DELETE")
     */
    public function destroyAction(Request $request, $id)
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
     * Prepare to delete this thing.
     *
     * @Route("/{id}/delete", name="clients_delete_confirm")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction(Client $client)
    {
        $deleteForm = $this->createDeleteForm($client->getId());
        return ['client'=>$client, 'delete_form'=>$deleteForm->createView()];
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
