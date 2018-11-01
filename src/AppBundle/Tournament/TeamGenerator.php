<?php

namespace AppBundle\Tournament;


use AppBundle\Entity\Team;

class TeamGenerator
{
    public function generate(int $count) {
        $teams = [];
        for ($number = 1; $number <= $count; $number++) {
            $teams[] = new Team("Team {$number}");
        }

        return new TeamCollection($teams);
    }
}
