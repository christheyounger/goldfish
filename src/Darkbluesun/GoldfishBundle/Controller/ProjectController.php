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
        $em = $this->getDoctrine()->getManager();
        $project = $this->get('serializer')->deserialize($request->getContent(), Project::class, 'json');
        $project->setWorkspace($this->getUser()->getWorkspace());
        $project = $em->merge($project);
        $project->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime());
        $em->flush();
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
        $em = $this->getDoctrine()->getManager();
        $created = $project->getCreatedAt();
        $project = $this->get('serializer')->deserialize($request->getContent(), Project::class, 'json');
        $project = $em->merge($project);
        $project->setCreatedAt($created)->setUpdatedAt(new \DateTime());
        $em->flush();
        return $this->getAction($project);
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
}
