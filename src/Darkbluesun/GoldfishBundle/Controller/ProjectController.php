<?php

namespace Darkbluesun\GoldfishBundle\Controller;

use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Darkbluesun\GoldfishBundle\Entity\Project;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

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
    public function getcAction()
    {
        return new Response(
            $this->get('serializer')->serialize(
                $this->getUser()->getWorkspace()->getProjects(), 'json',
                SerializationContext::create()->setGroups(['project_list'])
            ));
    }

    /**
     * Get a Project
     * @Security("is_granted('VIEW', project)")
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
    public function postAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $this->get('serializer')->deserialize($request->getContent(), Project::class, 'json');
        $project->setWorkspace($this->getUser()->getWorkspace());
        $project = $em->merge($project);
        $project->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime());
        $em->flush();

        $aclProvider = $this->get('security.acl.provider');
        $acl = $aclProvider->createAcl(ObjectIdentity::fromDomainObject($project));
        $acl->insertObjectAce(UserSecurityIdentity::fromAccount($this->getUser()), MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);

        return $this->getAction($project);
    }

    /**
     * Updates an existing Project entity.
     * @Security("is_granted('EDIT', project)")
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
     * @Security("is_granted('DELETE', project)")
     * @Route("/{id}", name="project_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Project $project)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();
        return new JsonResponse(['success']);
    }
}
