<?php


namespace Commune\Chatbot\Blueprint\Message\Transformed;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\TransformedMsg;

/**
 * 命令类消息
 */
interface CommandMsg extends Message, TransformedMsg, \ArrayAccess
{
    public function getCommandName() : string;

    public function isCorrect() : bool;

    public function getEntities() : array;

    public function getErrors(): array;

}