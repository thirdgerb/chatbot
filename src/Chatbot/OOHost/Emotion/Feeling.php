<?php


namespace Commune\Chatbot\OOHost\Emotion;


use Commune\Chatbot\Blueprint\Message\Message;

interface Feeling
{
    public function feel(Message $message, string $emotionName) : ? bool;

    /**
     * @param string $emotionName
     * @param string|callable $experience
     */
    public function experience(string $emotionName, $experience) : void;
}