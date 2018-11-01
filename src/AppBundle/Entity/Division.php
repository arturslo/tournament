<?php

namespace AppBundle\Entity;

use AppBundle\Tournament\TeamsAlreadyLockedException;
use AppBundle\Tournament\TeamSizeTooSmallException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="divisions")
 */
class Division
{
    const NAME_A = 'A';
    const NAME_B = 'B';

    const SATE_PICKING_TEAMS = 1;
    const SATE_IN_PROGRESS = 2;
    const SATE_FINISHED = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Team")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MatchResult", mappedBy="division")
     */
    private $matchResults;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tournament", inversedBy="divisions")
     */
    private $tournament;

    /**
     * Division constructor.
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->state = static::SATE_PICKING_TEAMS;

        $this->matchResults = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return ArrayCollection
     */
    public function getTeams() : ArrayCollection
    {
        return $this->teams;
    }

    /**
     * @return bool
     */
    public function isTeamsLocked(): bool
    {
        if ($this->state === static::SATE_PICKING_TEAMS) {
            return false;
        }

        return true;
    }

    /**
     * @param ArrayCollection $teams
     */
    public function setTeams(ArrayCollection $teams)
    {
        if ($this->isTeamsLocked() === true) {
            throw new \LogicException('Teams cannot be set when locked');
        }
        $this->teams = $teams;
    }

    /**
     * @return bool
     */
    public function canTeamsBeLocked() : bool
    {
        if ($this->isTeamSizeValid() === false) {
            return false;
        }

        if ($this->isTeamsLocked() === true) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isTeamSizeValid() : bool
    {
        if (count($this->teams) < 4) {
            return false;
        }

        return true;
    }

    public function lockTeams()
    {
        if ($this->isTeamSizeValid() === false) {
            throw new TeamSizeTooSmallException();
        }

        if ($this->isTeamsLocked() === true) {
            throw new TeamsAlreadyLockedException();
        }

        $this->state = static::SATE_IN_PROGRESS;
        $this->matchResults = $this->generateMatchResults($this->teams);
    }

    /**
     * @param $teams Team[]|ArrayCollection
     * @return MatchResult[]
     */
    private function generateMatchResults($teams)
    {
        $teamCount = count($teams);
        $matchResults = [];
        for($index = 0; $index < $teamCount - 1; $index++) {
            for ($index2 = $index + 1; $index2 < $teamCount; $index2++) {
                $matchResults[] = new MatchResult($teams[$index], $teams[$index2], null);
            }
        }

        return $matchResults;
    }

    /**
     * @param MatchResult $matchResult
     * @param Team $winnerTeam
     */
    public function setWinnerTeam(MatchResult $matchResult, Team $winnerTeam)
    {
        if ($this->state === static::SATE_FINISHED) {
            throw new \LogicException("Cannot set winner team when matches are finished");
        }

        $matchResult->setWinnerTeam($winnerTeam);
        $unplayedMatchCount = count($this->getUnplayedMatches());
        if ($unplayedMatchCount === 0) {
            $this->state = static::SATE_FINISHED;
        }
    }

    /**
     * @return MatchResult[]
     */
    public function getUnplayedMatches()
    {
        $unplayedMatches = [];
        foreach ($this->matchResults as $matchResult) {
            if ($matchResult->getWinnerTeam() === null) {
                $unplayedMatches[] = $matchResult;
            }
        }
        return $unplayedMatches;
    }

    /**
     * @param $id
     * @return Team|null
     */
    public function getTeamById($id)
    {
        $resultTeam = null;
        foreach ($this->teams as $team) {
            if ($team->getId() === $id) {
                $resultTeam = $team;
                break;
            }
        }
        return $resultTeam;
    }

    /**
     * @return MatchResult[]|ArrayCollection
     */
    public function getMatchResults()
    {
        return $this->matchResults;
    }

    /**
     * @return mixed
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * @param mixed $tournament
     */
    public function setTournament($tournament)
    {
        $this->tournament = $tournament;
    }

}