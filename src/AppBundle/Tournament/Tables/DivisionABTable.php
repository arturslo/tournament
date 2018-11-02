<?php

namespace AppBundle\Tournament\Tables;

use AppBundle\Entity\Division;
use AppBundle\Entity\MatchResult;

class DivisionABTable
{
    /**
     * @var Division
     */
    private $division;

    private $tableArray;
    private $gameArray;
    private $heading;
    private $headingColspan;

    public function __construct(Division $division)
    {
        $this->division = $division;
        $this->tableArray = [];
        $this->gameArray = [];

        $this->generateGameArray($this->division);
        $this->generateTableArray($this->division, $this->gameArray);
        $this->heading = 'Division' . $division->getName();
        $this->headingColspan = count($this->gameArray);
    }

    private function generateGameArray(Division $division)
    {
        $teams = $division->getTeams();
        $teamCount = count($teams);
        $matchResults = $division->getMatchResults();
        $matchResultIndex = 0;

        for ($index = 0; $index < $teamCount; $index++) {
            for ($index2 = 0; $index2 < $teamCount; $index2++) {
                if ($index === $index2) {
                    $this->gameArray[$index][$index2] = 'X';
                    continue;
                }

                if ($index2 > $index) {
                    $matchResult = $matchResults[$matchResultIndex];
                    $scoreString = $this->getScoreRepresentation($matchResult);
                    $this->gameArray[$index][$index2] = $scoreString;
                    $this->gameArray[$index2][$index] = $scoreString;
                }
            }
        }
    }

    /**
     * @param Division $division
     * @param array[][] $gameArray
     */
    public function generateTableArray(Division $division, $gameArray)
    {
        $teams = $division->getTeams();
        $teamCount = count($teams);

        for ($index = 0; $index < $teamCount + 1; $index++) {
            for ($index2 = 0; $index2 < $teamCount + 2; $index2++) {
                $this->tableArray[$index][$index2] = 'ERR';
            }
        }

        for ($index = 0; $index < $teamCount + 1; $index++) {
            $teamInRow = ($index - 1 >= 0) ? $teams[$index - 1] : null;
            for ($index2 = 0; $index2 < $teamCount + 2; $index2++) {

                if ($index > 0 && $index2 === 0) {
                    $this->tableArray[$index][$index2] = $teamInRow->getName();
                    $this->tableArray[$index2][$index] = $teamInRow->getName();
                    continue;
                }
                if ($index > 0 && $index2 > 0 && $index2 < $teamCount + 1) {
                    $this->tableArray[$index][$index2] = $gameArray[$index - 1][$index2 - 1];
                    continue;
                }

                if ($index > 0 && $index2 === $teamCount + 1) {
                    $this->tableArray[$index][$index2] = (string)$division->getTeamScore($teamInRow);
                }

            }
        }

        $this->tableArray[0][0] = 'TEAMS';
        $this->tableArray[0][$teamCount + 1] = 'SCORE';
    }

    /**
     * @param MatchResult $matchResult
     * @return string
     */
    private function getScoreRepresentation(MatchResult $matchResult): string
    {
        if ($matchResult->getWinnerTeam() === null) {
            return '';
        } elseif ($matchResult->getFirstTeam() === $matchResult->getWinnerTeam()) {
            return '1:0';
        } else {
            return '0:1';
        }
    }

    /**
     * @return Division
     */
    public function getDivision(): Division
    {
        return $this->division;
    }

    /**
     * @return array
     */
    public function getTableArray(): array
    {
        return $this->tableArray;
    }

    /**
     * @return array
     */
    public function getGameArray(): array
    {
        return $this->gameArray;
    }

    /**
     * @return string
     */
    public function getHeading(): string
    {
        return $this->heading;
    }

    /**
     * @return int
     */
    public function getHeadingColspan(): int
    {
        return $this->headingColspan;
    }

}