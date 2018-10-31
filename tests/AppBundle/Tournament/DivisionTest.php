<?php

namespace Tests\AppBundle\Tournament;

use AppBundle\Tournament\Team;
use AppBundle\Tournament\TeamCollection;
use PHPUnit\Framework\TestCase;
use AppBundle\Tournament\Division;
use ReflectionClass;
use AppBundle\Tournament\TeamSizeTooSmallException;
use AppBundle\Tournament\MatchResult;
use AppBundle\Tournament\MatchResultCollection;

class DivisionTest extends TestCase
{
    /**
     * @param int $teamCount
     * @return Division
     */
    public function getDivisionWithTeams(int $teamCount): Division
    {
        $teams = [];
        for ($number = 1; $number <= $teamCount; $number++) {
            $team = new Team("Team {$number}");
            $team->setId($number);
            $teams[] = $team;
        }
        $teamCollection = new TeamCollection($teams);
        $division = new Division(Division::NAME_A);
        $division->setTeams($teamCollection);
        return $division;
    }

    /**
     * @return Division
     */
    public function getDivisionWithMatchResults(): Division
    {
        $division = $this->getDivisionWithTeams(4);
        $division->lockTeams();
        $matchResultCollection = $division->getMatchResults();

        for ($index = 0; $index < count($matchResultCollection); $index++) {
            $this->setProperty($matchResultCollection[$index], 'id', $index + 1);
        }

        return $division;
    }

    public function setProperty($entity, $propertyName, $value)
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        $property->setValue($entity, $value);
    }

    public function test_can_get_name()
    {
        $division = new Division(Division::NAME_A);
        $this->assertEquals('A', $division->getName());

    }

    public function test_name_can_be_A()
    {
        $division = new Division(Division::NAME_A);
        $this->assertEquals('A', $division->getName());
    }

    public function test_name_can_be_B()
    {
        $division = new Division(Division::NAME_B);
        $this->assertEquals('B', $division->getName());
    }

    public function test_invalid_name_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Division('C');
    }

    public function test_can_set_and_get_id()
    {
        $division = new Division(Division::NAME_A);
        $division->setId(43);
        $this->assertEquals(43, $division->getId());
    }

    public function test_teams_can_be_added_and_retrieved()
    {
        $division = $this->getDivisionWithTeams(6);
        $this->assertEquals(6, count($division->getTeams()));
    }

    public function test_get_team_by_id()
    {
        $division = $this->getDivisionWithTeams(4);
        $team = $division->getTeamById(3);
        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals(3, $team->getId());
    }

    public function test_default_state_picking_teams()
    {
        $division = new Division(Division::NAME_A);
        $this->assertEquals(Division::SATE_PICKING_TEAMS, $division->getState());
    }

    public function test_lock_teams_sets_state_in_progress()
    {
        $division = $this->getDivisionWithTeams(4);
        $division->lockTeams();
        $this->assertEquals(Division::SATE_IN_PROGRESS, $division->getState());
    }

    public function test_canTeamsBeLocked_returns_true_if_team_count_larger_or_equal_than_4()
    {
        $division = $this->getDivisionWithTeams(4);
        $this->assertEquals(true, $division->canTeamsBeLocked());
    }

    public function test_canTeamsBeLocked_returns_false_if_team_count_smaller_than_4()
    {
        $division = $this->getDivisionWithTeams(2);
        $this->assertEquals(false, $division->canTeamsBeLocked());
    }

    public function test_lock_teams_throws_exception_if_canTeamsBeLocked_returns_false()
    {
        $division = $this->getDivisionWithTeams(2);
        $this->assertEquals(false, $division->canTeamsBeLocked());
        $this->expectException(TeamSizeTooSmallException::class);
        $division->lockTeams();
    }

    public function test_getMatchResults_return_empty_collection_when_state_is_picking_teams()
    {
        $division = new Division(Division::NAME_A);
        $this->assertEquals(Division::SATE_PICKING_TEAMS, $division->getState());
        $this->assertEquals(1, $this->count($division->getMatchResults()));
    }

    public function test_getMatchResults_returns_correct_count_after_team_lock()
    {
        $division = $this->getDivisionWithTeams(4);
        $division->lockTeams();
        $this->assertEquals(6, count($division->getMatchResults()));

        $division2 = $this->getDivisionWithTeams(5);
        $division2->lockTeams();
        $this->assertEquals(10, count($division2->getMatchResults()));
    }

    public function test_getMatchResults_returns_correct_type_after_team_lock()
    {
        $division = $this->getDivisionWithTeams(4);
        $division->lockTeams();
        $matchResults = $division->getMatchResults();
        $this->assertInstanceOf(MatchResultCollection::class, $matchResults);
    }

    public function test_getMatchResults_starting_values()
    {
        $division = $this->getDivisionWithTeams(4);
        $division->lockTeams();
        $matchResultCollection = $division->getMatchResults();

        $team1 = $division->getTeamById(1);
        $team2 = $division->getTeamById(2);
        $team3 = $division->getTeamById(3);
        $team4 = $division->getTeamById(4);

        $expectedMatchResultsArray = [
            new MatchResult($team1, $team2, null),
            new MatchResult($team1, $team3, null),
            new MatchResult($team1, $team4, null),
            new MatchResult($team2, $team3, null),
            new MatchResult($team2, $team4, null),
            new MatchResult($team3, $team4, null),
        ];

        $expectedMatchResultCollection = new MatchResultCollection($expectedMatchResultsArray);

        $this->assertEquals($expectedMatchResultCollection, $matchResultCollection);
    }

    public function test_set_winner()
    {
        $division = $this->getDivisionWithMatchResults();
        $matchResultCollection = $division->getMatchResults();

        $team1 = $division->getTeamById(1);
        $division->setWinnerTeam($matchResultCollection[1], $team1);

        $this->assertEquals($team1, $matchResultCollection[1]->getWinnerTeam());
    }

    public function test_unplayed_matches()
    {
        $division = $this->getDivisionWithMatchResults();
        $matchResultCollection = $division->getMatchResults();

        $team1 = $division->getTeamById(1);
        $division->setWinnerTeam($matchResultCollection[1], $team1);

        $expectedUnplayedMatches = [
            $matchResultCollection[0],
            $matchResultCollection[2],
            $matchResultCollection[3],
            $matchResultCollection[4],
            $matchResultCollection[5]
        ];

        $expectedMatchResultCollection = new MatchResultCollection($expectedUnplayedMatches);
        $this->assertEquals($expectedMatchResultCollection, $division->getUnplayedMatches());
    }

    public function test_when_all_winer_teams_are_set_division_state_finished()
    {
        $division = $this->getDivisionWithMatchResults();
        $matchResultCollection = $division->getMatchResults();
        foreach ($matchResultCollection as $matchResult){
            $division->setWinnerTeam($matchResult, $matchResult->getFirstTeam());
        }

        $this->assertEquals(Division::SATE_FINISHED, $division->getState());
    }

}