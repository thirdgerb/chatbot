<?php

namespace Commune\Chatbot\Blueprint\Message\QA;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\Tags\Transformed;

interface Answer extends Message, Transformed
{
    /**
     * @return int|string
     */
    public function getChoice();

    /**
     * @param int|string $choice
     * @return bool
     */
    public function hasChoice($choice) : bool;

    public function getOriginMessage() : Message;

    /**
     * @return mixed
     */
    public function toResult();

}