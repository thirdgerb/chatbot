<?php

/**
 * Class DirectionException
 * @package Commune\Chatbot\Framework\Directing
 */

namespace Commune\Chatbot\Framework\Directing;

class RedirectionBreak extends \LogicException
{
    protected $redirection;

    public function __construct(Location $location)
    {
        $this->redirection = $location;
        parent::__construct();
    }

    /**
     * @return Location
     */
    public function getRedirection(): Location
    {
        return $this->redirection;
    }
}