<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class TaskComment extends ProjectComment
{
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Task",inversedBy="comments")
     * @ORM\JoinColumn(name="task_id",referencedColumnName="id")
     */
    protected $task;

    /**
     * Set task
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Task $task
     * @return Comment
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
}

