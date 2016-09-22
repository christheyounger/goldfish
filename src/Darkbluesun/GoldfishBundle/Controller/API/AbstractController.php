<?php

namespace Darkbluesun\GoldfishBundle\Controller\API;

use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class AbstractController extends Controller implements ClassResourceInterface
{
	protected $em;
	protected $serializer;
	protected $aclProvider;

	public function setContainer(ContainerInterface $c = null) {
        parent::setContainer($c);
        $this->em = $this->getDoctrine()->getManager();
		$this->aclProvider = $this->get('security.acl.provider');
		$this->serializer = $this->get('serializer');	
	}

	public function restResponse($values, $groups = []) {
		return new Response(
            $this->serializer->serialize(
                    $values, 'json',
                    SerializationContext::create()->setGroups($groups)
            ));
	}

	public function newObjectPermission($obj, $mask = MaskBuilder::MASK_OWNER) {
        $acl = $this->aclProvider->createAcl(ObjectIdentity::fromDomainObject($obj));
        $acl->insertObjectAce(UserSecurityIdentity::fromAccount($this->getUser()), $mask);
        $this->aclProvider->updateAcl($acl);
	}
}