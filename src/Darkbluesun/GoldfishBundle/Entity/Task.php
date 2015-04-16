<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Darkbluesun\GoldfishBundle\Entity\TaskRepository")
 */
class Task
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
     * @ORM\Column(name="due", type="datetime")
     */
    private $due;

    /**
     * @var \Decimal
     *
     * @ORM\Column(name="time", type="decimal", scale=2)
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Workspace",inversedBy="tasks")
     * @ORM\JoinColumn(name="workspace_id",referencedColumnName="id")
     */
    protected $workspace;

    /**
     * @ORM\OneToMany(targetEntity="TimeEntry", mappedBy="task")
     */
    protected $timeEntries;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Client",inversedBy="tasks")
     * @ORM\JoinColumn(name="client_id",referencedColumnName="id")
     */
    protected $client;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Project",inversedBy="tasks")
     * @ORM\JoinColumn(name="project_id",referencedColumnName="id")
     */
    protected $project;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="tasks")
     * @ORM\JoinColumn(name="assignee_id",referencedColumnName="id")
     */
    protected $assignee;

    /**
     * @var boolean
     *
     * @ORM\OneToMany(targetEntity="TaskComment", mappedBy="task")
     */
    protected $comments;

    /**
     * Constructor function. Needed to initialise arrays of child objects
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->timeEntries = new ArrayCollection();
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
     * @return Task
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
     * Set time
     *
     * @param string $time
     * @return Task
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return string 
     */
    public function getTime()
    {
        return $this->time;
    }

    public function getTimeSpent() {
        $timesheet = $this->getTimeEntries();
        $time = 0;
        foreach ($timesheet as $entry) {
            $time += $entry->getLengthInt();
        }
        return $time;
    }

    public function getHoursSpent() {
        return round($this->getTimeSpent() / 60 / 60,2);
    }

    public function getBudgetColor() {
        if ($this->time <= 0) return "default";
        $progress = $this->getHoursSpent() / $this->time;
        if ($progress > 1) return "danger";
        else if ($progress > 0.8) return "warning";
        else return "default";
    }

    /**
     * Set due
     *
     * @param \DateTime $due
     * @return Task
     */
    public function setDue($due)
    {
        $this->due = $due;

        return $this;
    }

    /**
     * Get due
     *
     * @return \DateTime 
     */
    public function getDue()
    {
        return $this->due;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Task
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
     * @param \Darkbluesun\GoldfishBundle\Entity\Workspace $workspace
     * @return Task
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
     * @return Task
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
     * Set project
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Project $project
     * @return Task
     */
    public function setProject(\Darkbluesun\GoldfishBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Darkbluesun\GoldfishBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set assignee
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\User $assignee
     * @return Task
     */
    public function setAssignee(\Darkbluesun\GoldfishBundle\Entity\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \Darkbluesun\GoldfishBundle\Entity\User 
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Add comments
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\TaskComment $comments
     * @return Task
     */
    public function addComment(\Darkbluesun\GoldfishBundle\Entity\TaskComment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\TaskComment $comments
     */
    public function removeComment(\Darkbluesun\GoldfishBundle\Entity\TaskComment $comments)
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

    /**
     * Add timeEntries
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\TimeEntry $timeEntries
     * @return Task
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
