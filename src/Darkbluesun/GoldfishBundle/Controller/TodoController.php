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
 * Todo controller.
 *
 * @Route("/todos")
 */
class TodoController extends Controller
{

    /**
     * Lists all Todo entities.
     *
     * @Route("/", name="todos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * Lists all Todo entities.
     *
     * @Route("/api", name="todos_list")
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
            $data[] = $entity->__toArray();
        }

        return new JsonResponse($data);
    }

    /**
     * Creates a new Todo entity.
     *
     * @Route("/api", name="todos_create")
     * @Method("POST")
     * @Template("DarkbluesunGoldfishBundle:Todo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Todo();
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
     * Edits an existing Todo entity.
     *
     * @Route("/api/{id}", name="todos_update")
     * @Method("POST")
     * @Template("DarkbluesunGoldfishBundle:Todo:edit.html.twig")
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
     * Deletes a Todo entity.
     *
     * @Route("/api/{id}", name="todos_delete")
     * @Method("DELETE")
     */
    public function destroyAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DarkbluesunGoldfishBundle:Todo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Todo entity.');
            }

            $em->remove($entity);
            $em->flush();

            return new JsonResponse(['success'=>true]);
        }

    }

}
