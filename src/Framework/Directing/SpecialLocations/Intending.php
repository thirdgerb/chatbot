<?php

/**
 * Class Intending
 * @package Commune\Chatbot\Framework\Directing\SpecialLocations
 */

namespace Commune\Chatbot\Framework\Directing\SpecialLocations;


use Commune\Chatbot\Framework\Directing\Location;
use Commune\Chatbot\Framework\Intent\Intent;

class Intending extends Location
{
    /**
     * @var null|Intent
     */
    protected $predefinedIntent;

    public function __construct(Intent $intent)
    {
        $this->predefinedIntent = $intent;
        parent::__construct('', [], null);
    }

    /**
     * @return Intent|null
     */
    public function getPredefinedIntent(): ? Intent
    {
        return $this->predefinedIntent;
    }


}