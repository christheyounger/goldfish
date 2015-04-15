<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Workspace", mappedBy="users")
     */
    protected $workspaces;

    /**
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    protected $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    protected $lastName;

    /**
     * @ORM\Column(name="image", type="string", length=255)
     */
    protected $image;


    public function __construct()
    {
        $parent::__construct();
        $this->workspaces = new ArrayCollection();
    }

    public function getFirstName() {
        return $this->firstName;
    }
    public function setFirstName($value) {
        $this->firstName = $value;
        return $this;
    }
    public function getLastName() {
        return $this->lastName;
    }
    public function setLastName($value) {
        $this->lastName = $value;
        return $this;
    }

    /**
     * Add workspace
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Workspace $workspace
     * @return User
     */
    public function addWorkspace(\Darkbluesun\GoldfishBundle\Entity\Workspace $workspace)
    {
        $this->workspaces[] = $workspace;

        return $this;
    }

    /**
     * Remove workspace
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Workspace $workspace
     */
    public function removeWorkspace(\Darkbluesun\GoldfishBundle\Entity\Workspace $workspace)
    {
        $this->workspaces->removeElement($workspace);
    }

    /**
     * Get workspaces
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWorkspaces()
    {
        return $this->workspaces;
    }

    public function getWorkspace() {
        return $this->workspaces[0];
    }

    public function __toString() {
        return $this->firstName ? : $this->email;
    }
}
