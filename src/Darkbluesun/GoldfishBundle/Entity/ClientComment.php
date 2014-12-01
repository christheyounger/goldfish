<?php

namespace Darkbluesun\GoldfishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class ClientComment extends Comment
{
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Client",inversedBy="comments")
     * @ORM\JoinColumn(name="client_id",referencedColumnName="id")
     */
    protected $client;

    /**
     * Set client
     *
     * @param \Darkbluesun\GoldfishBundle\Entity\Client $client
     * @return Comment
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
