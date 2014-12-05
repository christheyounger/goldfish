<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * Plain password - don't save to database, just use for rego
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Workspace", mappedBy="users")
     */
    private $workspaces;

    public function __construct()
    {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
        $this->workspaces = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->email = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        $this->password = password_hash($this->plainPassword, PASSWORD_BCRYPT, array('cost' => 12));
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add roles
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Darkbluesun\GoldfishBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Role $roles
     */
    public function removeRole(\Darkbluesun\GoldfishBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Add workspaces
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Workspace $workspaces
     * @return User
     */
    public function addWorkspace(\Darkbluesun\GoldfishBundle\Entity\Workspace $workspaces)
    {
        $this->workspaces[] = $workspaces;

        return $this;
    }

    /**
     * Remove workspaces
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Workspace $workspaces
     */
    public function removeWorkspace(\Darkbluesun\GoldfishBundle\Entity\Workspace $workspaces)
    {
        $this->workspaces->removeElement($workspaces);
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
}
