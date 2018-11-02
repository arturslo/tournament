<?php

namespace AppBundle\Tournament;


use AppBundle\Entity\Division;
use AppBundle\Entity\MatchResult;
use AppBundle\Entity\Team;
use AppBundle\Entity\Tournament;

class TournamentStarter
{
    /**
     * @param Tournament $tournament
     */
    private function validateTournamentStart(Tournament $tournament)
    {
        if (count($tournament->getTeams()) < 8) {
            throw new TeamSizeTooSmallException();
        }

        if ($tournament->getState() !== Tournament::STATE_PICKED_TEAMS) {
            throw new \LogicException("Invalid state for this action");
        }
    }

    /**
     * @param Tournament $tournament
     */
    private function addDivisions(Tournament $tournament)
    {
        $divisionA = new Division(Division::NAME_A);
        $divisionB = new Division(Division::NAME_B);
        $tournament->setDivisions([$divisionA, $divisionB]);
    }

    /**
     * @param $teams Team[]
     * @return array
     */
    private function divideTeamsForDivisions($teams)
    {
        $teamsAB = ['A' => [], 'B' => []];
        for($index = 0; $index < count($teams); $index++) {
            if ($index % 2 == 0) {
                $teamsAB['A'][] = $teams[$index];
            } else {
                $teamsAB['B'][] = $teams[$index];
            }
        }

        return $teamsAB;
    }

    /**
     * @param Tournament $tournament
     */
    private function addTeamsToDivisions(Tournament $tournament)
    {
        $teamsAB = $this->divideTeamsForDivisions($tournament->getTeams());
        $divisionA = $tournament->getDivisionByName(Division::NAME_A);
        $divisionB = $tournament->getDivisionByName(Division::NAME_B);
        $divisionA->setTeams($teamsAB['A']);
        $divisionB->setTeams($teamsAB['B']);
    }


    private function generateMatchResults(Division $division)
    {
        $teams = $division->getTeams();
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
     * @param Tournament $tournament
     */
    private function setInitialMatchResults(Tournament $tournament)
    {
        $divisionA = $tournament->getDivisionByName(Division::NAME_A);
        $divisionB = $tournament->getDivisionByName(Division::NAME_B);
        $matchResultsA = $this->generateMatchResults($divisionA);
        $matchResultsB = $this->generateMatchResults($divisionB);
        $divisionA->setMatchResults($matchResultsA);
        $divisionB->setMatchResults($matchResultsB);
    }

    /**
     * @param Tournament $tournament
     */
    public function startTornament(Tournament $tournament)
    {
        $this->validateTournamentStart($tournament);
        $this->addDivisions($tournament);
        $this->addTeamsToDivisions($tournament);
        $this->setInitialMatchResults($tournament);
        $tournament->setState(Tournament::STATE_PLAYING_A_B);
    }
}