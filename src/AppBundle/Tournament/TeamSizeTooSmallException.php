<?php

namespace AppBundle\Tournament;

class TeamSizeTooSmallException extends \LogicException
{

    /**
     * TeamSizeTooSmallException constructor.
     */
    public function __construct($code = 0, \Throwable $previous = null) {
        $message = "Team size must be equal or larger than 4";
        parent::__construct($message, $code, $previous);
    }
}