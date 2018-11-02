<?php

namespace AppBundle\Tournament;


use AppBundle\Entity\Division;
use AppBundle\Entity\MatchResult;
use AppBundle\Entity\Team;
use AppBundle\Entity\Tournament;

class TournamentManager
{

    /**
     * @var TournamentStarter
     */
    private $tournamentStarter;

    public function __construct(TournamentStarter $tournamentStarter)
    {
        $this->tournamentStarter = $tournamentStarter;
    }

    /**
     * @param Tournament $tournament
     */
    public function startTornament(Tournament $tournament)
    {
        $this->tournamentStarter->startTornament($tournament);
    }

}