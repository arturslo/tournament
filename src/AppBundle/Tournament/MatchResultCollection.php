<?php

namespace AppBundle\Tournament;


class MatchResultCollection extends \ArrayObject
{
    /**
     * @return MatchResultCollection
     */
    public function getUnplayedMatches()
    {
        $unplayedMatches = [];
        foreach ($this->getIterator() as $matchResult) {
            if ($matchResult->getWinnerTeam() === null) {
                $unplayedMatches[] = $matchResult;
            }
        }
        return new static($unplayedMatches);
    }

}