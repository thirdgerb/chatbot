<?php


namespace Commune\Chatbot\Blueprint\Message\Command;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\Tags\Transformed;

interface CmdMessage extends Message, Transformed, \ArrayAccess
{
    public function getCommandName() : string;

    public function isCorrect() : bool;

    public function getEntities() : array;

    public function getErrors(): array;

}