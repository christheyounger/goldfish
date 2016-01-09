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
     */
    public function indexAction()
    {
        return new Response(
            $this->get('serializer')->serialize(
                $this->getUser()->getWorkspace()->getProjects(), 'json',
                SerializationContext::create()->setGroups(['project_list'])
            ));
    }

    /**
     * Get a Project
     *
     * @Route("/{id}", name="project_get")
     * @Method("GET")
     */
    public function getAction(Project $project)
    {
        return new Response($this->get('serializer')->serialize($project,'json',SerializationContext::create()->setGroups(['project_details'])));
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("", name="project_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $project = new Project();
        $project->setWorkspace($this->getUser()->getWorkspace());
        $this->applyData($project,(array)json_decode($request->getContent()),['name','budget','dueDate','client']);
        return $this->getAction($project);
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/{id}", name="project_update")
     * @Method("POST")
     */
    public function updateAction(Request $request, Project $project)
    {
        $this->requireWorkspace($project);
        $this->applyData($project,(array)json_decode($request->getContent()),['name','budget','dueDate','client']);
        return $this->getAction($project);
    }

    /**
     * Task quick add
     *
     * @Route("/{id}/task/", name="project_quick_task")
     * @Method("POST")
     */
    public function addTaskAction(Request $request, Project $project) {
        $this->requireWorkspace($project);
        $em = $this->getDoctrine()->getManager();
        $task = new Task();
        $task->setProject($project);
        $task->setName($request->request->get('name'));
        $task->setDue(new \DateTime($request->request->get('due')));
        $task->setTime($request->request->get('time'));
        $em->persist($task);
        $em->flush();
        return new Response($this->get('serializer')->serialize($task,'json',SerializationContext::create()->setGroups(['task_details'])));
    }
    /**
     * Deletes a Project.
     *
     * @Route("/{id}", name="project_delete")
     * @Method("DELETE")
     */
    public function destroyAction(Request $request, Project $project)
    {
        $this->requireWorkspace($project);
        $em->remove($project);
        $em->flush();
        return new JsonResponse(['success']);
    }

    public function requireWorkspace($project) {
        if (!$this->checkWorkspace($project)) {
            $this->createAccessDeniedException();
        }
    }

    public function checkWorkspace($project) {
        foreach ($this->getUser()->getWorkspaces() as $w) {
            if ($w->getProjects()->contains($project)) return true;
        }
    }

    private function applyData(Project $project, Array $data, Array $allowed) {
        $em = $this->getDoctrine()->getManager();
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                // Transform
                switch ($key) {
                    case 'dueDate': $data[$key] = new \DateTime($data[$key]); break;
                    case 'client': $data[$key] = $em->find(Client::class,$data[$key]->id); break;
                }
                // Set
                $setter = 'set'.ucfirst($key);
                $project->$setter($data[$key]);
            }
        }
        if (!$project->getId()) $em->persist($project);
        $em->flush();
    }
}
