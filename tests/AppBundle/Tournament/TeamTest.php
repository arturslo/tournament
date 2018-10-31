<?php

namespace Tests\AppBundle\Tournament;

use PHPUnit\Framework\TestCase;
use AppBundle\Tournament\Team;

class TeamTest extends TestCase
{
    public function test_can_get_team_name()
    {
        $team = new Team("My team");
        $this->assertEquals('My team', $team->getName());
    }

    public function test_empty_team_name_string_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Team('');
    }

    public function test_can_set_and_get_id()
    {
        $team = new Team("My team");
        $team->setId(14);
        $this->assertEquals(14, $team->getId());
    }

}