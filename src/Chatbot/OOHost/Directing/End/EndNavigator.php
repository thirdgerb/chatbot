<?php

/**
 * Class EndNavigator
 * @package Commune\Chatbot\OOHost\Directing\End
 */

namespace Commune\Chatbot\OOHost\Directing\End;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;

abstract class EndNavigator extends AbsNavigator
{

    abstract public function beingHeard() : bool;
}