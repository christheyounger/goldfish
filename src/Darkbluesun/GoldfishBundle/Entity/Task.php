<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation as Serial;

/**
 * Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TaskRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Task
{
    use TimestampableEntity;
    use SoftDeleteableEntity;
    /**
     * @var integer
     *
     * @Serial\Groups({"task_list","task_details","project_details","client_details"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @Serial\Groups({"task_list","task_details","project_details","client_details"})
     * @ORM\Column(name="done", type="boolean")
     */
    private $done = false;

    /**
     * @var string
     *
     * @Serial\Groups({"task_list","task_details","project_details","client_details"})
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     * @ORM\Column(name="due", type="datetime", nullable=true)
     * @Serial\Groups({"task_list","task_details","client_details"})
     */
    private $dueDate;

    /**
     * @var \Decimal
     *
     * @Serial\Groups({"task_list","task_details","project_details","client_details"})
     * @ORM\Column(name="time", type="decimal", scale=2, nullable=true)
     */
    private $time;

    /**
     * @var string
     *
     * @Serial\Groups({"task_details"})
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @Serial\Groups({"task_details","task_list"})
     * @ORM\ManyToOne(targetEntity="Workspace",inversedBy="tasks")
     * @ORM\JoinColumn(name="workspace_id",referencedColumnName="id")
     * @Serial\Type("Darkbluesun\GoldfishBundle\Entity\Workspace")
     */
    protected $workspace;

    /**
     * @Serial\Groups({"task_details"})
     * @ORM\OneToMany(targetEntity="TimeEntry", mappedBy="task")
     */
    protected $timeEntries;

    /**
     * @Serial\Groups({"task_list","task_details","project_details"})
     * @ORM\ManyToOne(targetEntity="Client",inversedBy="tasks")
     * @ORM\JoinColumn(name="client_id",referencedColumnName="id", onDelete="SET NULL")
     * @Serial\Type("Darkbluesun\GoldfishBundle\Entity\Client")
     */
    protected $client;

    /**
     * @Serial\Groups({"task_list","task_details"})
     * @ORM\ManyToOne(targetEntity="Project",inversedBy="tasks")
     * @ORM\JoinColumn(name="project_id",referencedColumnName="id", onDelete="SET NULL")
     * @Serial\Type("Darkbluesun\GoldfishBundle\Entity\Project")
     */
    protected $project;

    /**
     * @Serial\Groups({"task_list","task_details","project_details","client_details"})
     * @ORM\ManyToOne(targetEntity="User",inversedBy="tasks")
     * @ORM\JoinColumn(name="assignee_id",referencedColumnName="id")
     * @Serial\Type("Darkbluesun\GoldfishBundle\Entity\User")
     */
    protected $assignee;

    /**
     * @Serial\Groups({"task_details"})
     * @ORM\OneToMany(targetEntity="TaskComment", mappedBy="task", cascade="remove")
     * @Serial\Type("Darkbluesun\GoldfishBundle\Entity\TaskComment")
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

    public function __toString()
    {
        return $this->name;
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
     * Set done
     *
     * @param boolean $done
     * @return Task
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return boolean 
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * Get done
     *
     * @return boolean 
     */
    public function getDone()
    {
        return $this->done;
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
     * @param Workspace $workspace
     * @return Task
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
     * Set client
     *
     * @param Client $client
     * @return Task
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;
        $this->project = null;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set project
     *
     * @param Project $project
     * @return Task
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;
        $this->client = null;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set assignee
     *
     * @param User $assignee
     * @return Task
     */
    public function setAssignee(User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return User 
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Add comment.
     *
     * @param TaskComment $comment
     *
     * @return Task
     */
    public function addComment(TaskComment $comment)
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTask($this);
        }

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param TaskComment $comment
     *
     * @return Task
     */
    public function removeComment(TaskComment $comment)
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    /**
     * Get comments.
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add timeEntry.
     *
     * @param TimeEntry $timeEntry
     * @return Task
     */
    public function addTimeEntry(TimeEntry $timeEntry)
    {
        if (!$this->timeEntries->contains($timeEntry)) {
            $this->timeEntries->add($timeEntry);
            $timeEntry->setTask($this);
        }

        return $this;
    }

    /**
     * Remove timeEntry.
     *
     * @param TimeEntry $timeEntry
     *
     * @return Task
     */
    public function removeTimeEntry(TimeEntry $timeEntry)
    {
        $this->timeEntries->removeElement($timeEntry);

        return $this;
    }

    /**
     * Get timeEntries.
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTimeEntries()
    {
        return $this->timeEntries;
    }
}
