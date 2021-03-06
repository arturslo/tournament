<?php

namespace Tests\AppBundle\Tournament;

use AppBundle\Entity\Team;
use PHPUnit\Framework\TestCase;

class TeamTest extends TestCase
{
    public function test_can_get_team_name()
    {
        $team = new Team("My team");
        $this->assertEquals('My team', $team->getName());
    }

    public function test_can_set_and_get_id()
    {
        $team = new Team("My team");
        $team->setId(14);
        $this->assertEquals(14, $team->getId());
    }

}