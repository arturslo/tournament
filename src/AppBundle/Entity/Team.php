<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="teams")
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;
    /**
    * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tournament", mappedBy="teams")
    */
    private $tournaments;

    /**
     * Team constructor.
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->tournaments = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $name|null
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTournaments()
    {
        return $this->tournaments;
    }

    /**
     * @param mixed $tournaments
     */
    public function setTournaments($tournaments)
    {
        $this->tournaments = $tournaments;
    }

    /**
     * @param Tournament $tournament
     */
    public function addTournament(Tournament $tournament)
    {
        if ($this->tournaments->contains($tournament)) {
            return;
        }
        $this->tournaments->add($tournament);
        $tournament->addTeam($this);
    }

}