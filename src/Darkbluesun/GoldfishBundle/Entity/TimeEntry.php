<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serial;

/**
 * TimeEntry
 *
 * @ORM\Table("TimeLog")
 * @ORM\Entity(repositoryClass="Darkbluesun\GoldfishBundle\Entity\TimeEntryRepository")
 */
class TimeEntry
{
    /**
     * @var integer
     *
     * @Serial\Groups({"default"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Serial\Groups({"task_details"})
     * @ORM\ManyToOne(targetEntity="User",inversedBy="timeEntries")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Task",inversedBy="timeEntries")
     * @ORM\JoinColumn(name="task_id",referencedColumnName="id", nullable=true)
     */
    protected $task;

    /**
     * @ORM\ManyToOne(targetEntity="Project",inversedBy="timeEntries")
     * @ORM\JoinColumn(name="project_id",referencedColumnName="id", nullable=true)
     */
    protected $project;

    /**
     * @ORM\ManyToOne(targetEntity="Client",inversedBy="timeEntries")
     * @ORM\JoinColumn(name="client_id",referencedColumnName="id", nullable=true)
     */
    protected $client;

    /**
     * @var \DateTime
     *
     * @Serial\Groups({"task_details"})
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @Serial\Groups({"task_details"})
     * @ORM\Column(name="end", type="datetime")
     */
    private $end;

    /**
     * @var string
     *
     * @Serial\Groups({"task_details"})
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private $comment;


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
     * Set start
     *
     * @param \DateTime $start
     * @return TimeEntry
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return TimeEntry
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    public function getLength() {
        return date_diff($this->end,$this->start);
    }

    public function getLengthInt() {
        return $this->end->getTimestamp() - $this->start->getTimestamp();
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return TimeEntry
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set user
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\User $user
     * @return TimeEntry
     */
    public function setUser(\Darkbluesun\GoldfishBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Darkbluesun\GoldfishBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set task
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Task $task
     * @return TimeEntry
     */
    public function setTask(\Darkbluesun\GoldfishBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Darkbluesun\GoldfishBundle\Entity\Task 
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set project
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Project $project
     * @return TimeEntry
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
     * Set client
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Client $client
     * @return TimeEntry
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
}
