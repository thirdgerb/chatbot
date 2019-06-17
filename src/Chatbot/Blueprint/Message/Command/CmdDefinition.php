<?php


namespace Commune\Chatbot\Blueprint\Message\Command;


use Commune\Chatbot\Blueprint\Message\Message;
use Symfony\Component\Console\Input\InputArgument;

interface CmdDefinition
{
    public function getCommandName() : string;

    public function toCommandMessage(string $cmdText, Message $message) : CmdMessage;

    /**
     * Gets the array of InputArgument objects.
     *
     * @return InputArgument[] An array of InputArgument objects
     */
    public function getArguments();
}