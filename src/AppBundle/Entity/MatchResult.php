<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Team;

class MatchResult
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Team
     */
    private $firstTeam;
    /**
     * @var Team
     */
    private $secondTeam;
    /**
     * @var Team
     */
    private $winnerTeam;
    /**
     * @var Team
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

}