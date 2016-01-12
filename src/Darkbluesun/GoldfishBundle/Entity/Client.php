<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use JMS\Serializer\Annotation as Serial;

/**
 * Client
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Client
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @Serial\Groups({"client_list","client_details","project_list","project_details","task_list","task_details"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Serial\Groups({"client_list","client_details","project_list","project_details","task_list","task_details"})
     * @ORM\Column(name="companyName", type="string", length=255)
     */
    private $companyName;

    /**
     * @var string
     *
     * @Serial\Groups({"client_details"})
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @Serial\Groups({"client_list","client_details"})
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @Serial\Groups({"client_list","client_details"})
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @Serial\Groups({"client_list","client_details"})
     * @ORM\Column(name="contactName", type="string", length=255, nullable=true)
     */
    private $contactName;

    /**
     * @var string
     *
     * @Serial\Groups({"client_list","client_details"})
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @Serial\Groups({"client_details"})
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Workspace",inversedBy="clients")
     * @ORM\JoinColumn(name="workspace_id",referencedColumnName="id")
     * @Serial\Groups({"client_details", "client_list"})
     */
    protected $workspace;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="client")
     * @Serial\Groups({"client_details"})
     */
    protected $projects;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="client")
     * @Serial\Groups({"client_details"})
     */
    protected $tasks;

    /**
     * @ORM\OneToMany(targetEntity="ClientComment", mappedBy="client", cascade="remove")
     * @Serial\Groups({"client_details"})
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="TimeEntry", mappedBy="client")
     * @Serial\Groups({"client_details"})
     */
    protected $timeEntries;

    /**
     * Constructor function. Needed to initialise arrays of child objects
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * Set companyName
     *
     * @param string $companyName
     * @return Client
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string 
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Client
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Client
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Client
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
     * Set contactName
     *
     * @param string $contactName
     * @return Client
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string 
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Client
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Client
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set workspace
     *
     * @param Workspace $workspace
     * @return Client
     */
    public function setWorkspace(Workspace $workspace = null)
    {
        $this->workspace = $workspace;

        return $this;
    }

    /**
     * Get workspace
     *
     * @return Workspace 
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * Add projects
     *
     * @param Project $projects
     * @return Client
     */
    public function addProject(Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param Project $projects
     */
    public function removeProject(Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add tasks
     *
     * @param Task $tasks
     * @return Client
     */
    public function addTask(Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param Task $tasks
     */
    public function removeTask(Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks($local=true)
    {
        if (!$local) return $this->tasks;

        // Get only the tasks that aren't part of a project
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('project', NULL));
        return $this->tasks->matching($criteria);
    }

    /**
     * Add comments
     *
     * @param ClientComment $comments
     * @return Client
     */
    public function addComment(ClientComment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param ClientComment $comments
     */
    public function removeComment(ClientComment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function __toString() {
        return $this->companyName;
    }

    /**
     * Add timeEntries
     *
     * @param TimeEntry $timeEntries
     * @return Client
     */
    public function addTimeEntry(TimeEntry $timeEntries)
    {
        $this->timeEntries[] = $timeEntries;

        return $this;
    }

    /**
     * Remove timeEntries
     *
     * @param TimeEntry $timeEntries
     */
    public function removeTimeEntry(TimeEntry $timeEntries)
    {
        $this->timeEntries->removeElement($timeEntries);
    }

    /**
     * Get timeEntries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTimeEntries()
    {
        return $this->timeEntries;
    }
}
