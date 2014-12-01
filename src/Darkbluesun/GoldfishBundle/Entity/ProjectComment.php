<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class ProjectComment extends ClientComment
{
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Project",inversedBy="comments")
     * @ORM\JoinColumn(name="project_id",referencedColumnName="id")
     */
    protected $project;

    /**
     * Set project
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Project $project
     * @return Comment
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
}
