<?php

namespace Darkbluesun\GoldfishBundle\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Darkbluesun\GoldfishBundle\Entity\Project;

class ProjectController extends AbstractController
{
    public function cgetAction()
    {
        return $this->restResponse($this->getUser()->getWorkspace()->getProjects(), ['project_list']);
    }

    /**
     * @Security("is_granted('VIEW', project)")
     */
    public function getAction(Project $project)
    {
        return $this->restResponse($project, ['project_details']);
    }

    public function postAction(Request $request)
    {
        $project = $this->serializer->deserialize($request->getContent(), Project::class, 'json');
        $project->setWorkspace($this->getUser()->getWorkspace());
        $project = $this->em->merge($project);
        $project->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime());
        $this->em->flush();

        $this->newObjectPermission($project);

        return $this->getAction($project);
    }

    /**
     * @Security("is_granted('EDIT', project)")
     */
    public function putAction(Request $request, Project $project)
    {
        $created = $project->getCreatedAt();
        $project = $this->serializer->deserialize($request->getContent(), Project::class, 'json');
        $project = $this->em->merge($project);
        $project->setCreatedAt($created)->setUpdatedAt(new \DateTime());
        $this->em->flush();
        return $this->getAction($project);
    }

    /**
     * @Security("is_granted('DELETE', project)")
     */
    public function deleteAction(Project $project)
    {
        $this->em->remove($project);
        $this->em->flush();
    }
}
