<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Darkbluesun\GoldfishBundle\Entity\ProjectRepository")
 */
class Project
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="datetimetz")
     */
    private $dueDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="budget", type="integer")
     */
    private $budget;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Workspace",inversedBy="projects")
     * @ORM\JoinColumn(name="workspace_id",referencedColumnName="id")
     */
    protected $workspace;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Client",inversedBy="projects")
     * @ORM\JoinColumn(name="client_id",referencedColumnName="id", onDelete="SET NULL")
     */
    protected $client;

    /**
     * @var boolean
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="project")
     */
    protected $tasks;

    /**
     * @var boolean
     *
     * @ORM\OneToMany(targetEntity="ProjectComment", mappedBy="project")
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="TimeEntry", mappedBy="project")
     */
    protected $timeEntries;

    /**
     * Constructor function. Needed to initialise arrays of child objects
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->timeEntries = new ArrayCollection();
    }

    public function __toArray() {
        $data = [
            'id' => $this->getId(),
            'client' => ['id'=>$this->client?$this->client->getId():'',
                         'name'=>(String)$this->client],
            'name' => $this->getName(),
            'budget' => $this->getBudget(),
            'due' => [
                'timestamp' => $this->getDueDate()->format('U'),
                'string' => $this->getDueDate()->format('d/m/y ha')
            ],
          ];
        return $data;
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
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     * @return Project
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime 
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set budget
     *
     * @param integer $budget
     * @return Project
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return integer 
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Set workspace
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Workspace $workspace
     * @return Project
     */
    public function setWorkspace(\Darkbluesun\GoldfishBundle\Entity\Workspace $workspace = null)
    {
        $this->workspace = $workspace;

        return $this;
    }

    /**
     * Get workspace
     *
     * @return \Darkbluesun\GoldfishBundle\Entity\Workspace 
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * Set client
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Client $client
     * @return Project
     */
    public function setClient(\Darkbluesun\GoldfishBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Darkbluesun\GoldfishBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Add tasks
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Task $tasks
     * @return Project
     */
    public function addTask(\Darkbluesun\GoldfishBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Task $tasks
     */
    public function removeTask(\Darkbluesun\GoldfishBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add comments
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\ProjectComment $comments
     * @return Project
     */
    public function addComment(\Darkbluesun\GoldfishBundle\Entity\ProjectComment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\ProjectComment $comments
     */
    public function removeComment(\Darkbluesun\GoldfishBundle\Entity\ProjectComment $comments)
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
        return $this->name;
    }

    /**
     * Add timeEntries
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\TimeEntry $timeEntries
     * @return Project
     */
    public function addTimeEntry(\Darkbluesun\GoldfishBundle\Entity\TimeEntry $timeEntries)
    {
        $this->timeEntries[] = $timeEntries;

        return $this;
    }

    /**
     * Remove timeEntries
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\TimeEntry $timeEntries
     */
    public function removeTimeEntry(\Darkbluesun\GoldfishBundle\Entity\TimeEntry $timeEntries)
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
