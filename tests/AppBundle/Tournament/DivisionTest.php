<?php

namespace Tests\AppBundle\Tournament;

use AppBundle\Entity\Division;
use AppBundle\Entity\MatchResult;
use AppBundle\Entity\Team;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Doctrine\Common\Collections\ArrayCollection;

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
        $teamCollection = new ArrayCollection($teams);
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
        $this->assertEquals(Division::SATE_IN_PROGRESS, $division->getState());
    }

    public function test_getMatchResults_returns_correct_count()
    {
        $division = $this->getDivisionWithTeams(2);
        $teams = $division->getTeams();
        $division->setMatchResults([
            new MatchResult($teams[0], $teams[1], null),
            new MatchResult($teams[0], $teams[1], null)
        ]);
        $this->assertEquals(2, count($division->getMatchResults()));
    }

}