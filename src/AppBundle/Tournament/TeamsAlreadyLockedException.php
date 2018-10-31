<?php

namespace AppBundle\Tournament;

class TeamsAlreadyLockedException extends \LogicException
{

    /**
     * TeamsAlreadyLockedException constructor.
     */
    public function __construct($code = 0, \Throwable $previous = null) {
        $message = "Teams already locked";
        parent::__construct($message, $code, $previous);
    }
}