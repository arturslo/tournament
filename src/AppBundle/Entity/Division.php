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

    const SATE_IN_PROGRESS = 1;
    const SATE_FINISHED = 2;

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
        $this->state = static::SATE_IN_PROGRESS;

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
     * @return ArrayCollection|Team[]
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param ArrayCollection|Team[] $teams
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
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
     * @param Team $team
     * @return int
     */
    public function getTeamScore(Team $team) : int
    {
        $teamScore = 0;
        foreach ($this->matchResults as $matchResult) {
            $winnerTeam = $matchResult->getWinnerTeam();
            if ($winnerTeam === null) {
                continue;
            }
            if ($team->getId() === $winnerTeam->getId()) {
                $teamScore++;
            }
        }

        return $teamScore;
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
     * @param mixed $matchResults
     */
    public function setMatchResults($matchResults)
    {
        foreach ($matchResults as $matchResult) {
            $matchResult->setDivision($this);
        }
        $this->matchResults = $matchResults;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
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