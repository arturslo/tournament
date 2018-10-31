<?php

namespace AppBundle\Tournament;


class TeamCollection extends \ArrayObject
{
    /**
     * @param int $id
     * @return Team|null
     */
    public function getTeamById(int $id)
    {
        $foundTeam = null;
        foreach ($this->getIterator() as $team) {
            if ($id === $team->getId()) {
                $foundTeam = $team;
                break;
            }
        }

        return $foundTeam;
    }
}