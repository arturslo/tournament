<?php

namespace AppBundle\Entity;

use AppBundle\Tournament\TeamsAlreadyLockedException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="tournaments")
 */
class Tournament
{
    const STATE_PICKING_TEAMS = 1;
    const STATE_PICKED_TEAMS = 2;
    const STATE_PLAYING_A_B = 3;
    const STATE_PLAYING_A = 4;
    const STATE_PLAYING_B = 5;
    const STATE_PLAYING_EXCLUSION = 6;
    const STATE_PLAYING_SEMI_FINAL = 7;
    const STATE_PLAYING_FINAL = 8;
    const STATE_FINISHED = 9;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Team")
     * @Assert\Count(min="8")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Division", mappedBy="tournament")
     */
    private $divisions;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    public function __construct()
    {
        $this->state = static::STATE_PICKING_TEAMS;
        $this->teams = new ArrayCollection();
        $this->divisions = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param mixed $teams
     */
    public function setTeams($teams)
    {
        if ($this->state !== static::STATE_PICKING_TEAMS) {
            throw new TeamsAlreadyLockedException();
        }
        $this->teams = $teams;
    }

    /**
     * @param Team $team
     */
    public function addTeam(Team $team)
    {
        if ($this->teams->contains($team)) {
            return;
        }
        $this->teams->add($team);
        $team->addTournament($this);
    }

    /**
     * @return ArrayCollection|Division[]
     */
    public function getDivisions()
    {
        return $this->divisions;
    }

    /**
     * @param mixed $divisions
     */
    public function setDivisions($divisions)
    {
        foreach ($divisions as $division) {
            $division->setTournament($this);
        }
        $this->divisions = $divisions;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @param string $name
     * @return Division|null
     */
    public function getDivisionByName(string $name)
    {
        $foundDivision = null;
        foreach ($this->divisions as $division) {
            if ($division->getName() === $name) {
                $foundDivision = $division;
                break;
            }
        }

        return $foundDivision;
    }
}