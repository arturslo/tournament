<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="match_results")
 */
class MatchResult
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Division", inversedBy="matchResults")
     */
    private $division;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team")
     */
    private $firstTeam;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team")
     */
    private $secondTeam;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team")
     */
    private $winnerTeam;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team")
     */
    private $loserTeam;

    /**
     * MatchResult constructor.
     * @param Team $firstTeam
     * @param Team $secondTeam
     * @param Team|null $winnerTeam
     */
    public function __construct(Team $firstTeam, Team $secondTeam, $winnerTeam = null)
    {
        $this->firstTeam = $firstTeam;
        $this->secondTeam = $secondTeam;
        $this->winnerTeam = $winnerTeam;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Team
     */
    public function getFirstTeam(): Team
    {
        return $this->firstTeam;
    }

    /**
     * @return Team
     */
    public function getSecondTeam(): Team
    {
        return $this->secondTeam;
    }

    /**
     * @return Team|null
     */
    public function getWinnerTeam()
    {
        return $this->winnerTeam;
    }

    /**
     * @return Team|null
     */
    public function getLoserTeam()
    {
        return $this->loserTeam;
    }

    /**
     * @param Team $winnerTeam
     */
    public function setWinnerTeam(Team $winnerTeam)
    {
        $teamIds = [$this->firstTeam->getId(), $this->secondTeam->getId()];

        if (!in_array($winnerTeam->getId(), $teamIds)) {
            throw new \LogicException("Winner id dosent match team ids");
        }

        $this->winnerTeam = $winnerTeam;
        if ($this->firstTeam->getId() === $winnerTeam->getId()) {
            $this->loserTeam = $this->secondTeam;
        } else {
            $this->loserTeam = $this->firstTeam;
        }
    }

    /**
     * @return mixed
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * @param mixed $division
     */
    public function setDivision($division)
    {
        $this->division = $division;
    }

}