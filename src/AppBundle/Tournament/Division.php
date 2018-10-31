<?php

namespace AppBundle\Tournament;


class Division
{
    const NAME_A = 'A';
    const NAME_B = 'B';

    const SATE_PICKING_TEAMS = 1;
    const SATE_IN_PROGRESS = 2;
    const SATE_FINISHED = 3;

    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $state;
    /**
     * @var int
     */
    private $id;
    /**
     * @var TeamCollection
     */
    private $teams;

    /**
     * @var MatchResultCollection
     */
    private $matchResults;

    /**
     * Division constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        if ($name !== static::NAME_A && $name !== static::NAME_B) {
            throw new \InvalidArgumentException("Division name can be A or B");
        }

        $this->name = $name;
        $this->state = static::SATE_PICKING_TEAMS;
        $this->teams = new TeamCollection([]);
        $this->matchResults = new MatchResultCollection([]);
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
     * @return TeamCollection
     */
    public function getTeams(): TeamCollection
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
     * @param TeamCollection $teams
     */
    public function setTeams(TeamCollection $teams)
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
     * @param $teams TeamCollection
     * @return MatchResultCollection
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

        return new MatchResultCollection($matchResults);
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
        $unplayedMatchCount = count($this->matchResults->getUnplayedMatches());
        if ($unplayedMatchCount === 0) {
            $this->state = static::SATE_FINISHED;
        }
    }

    /**
     * @return MatchResultCollection
     */
    public function getUnplayedMatches()
    {
        return $this->matchResults->getUnplayedMatches();
    }

    /**
     * @param $id
     * @return Team|null
     */
    public function getTeamById($id)
    {
        return $this->teams->getTeamById($id);
    }

    /**
     * @return MatchResultCollection
     */
    public function getMatchResults()
    {
        return $this->matchResults;
    }
}