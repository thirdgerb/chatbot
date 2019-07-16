<?php


namespace Commune\Chatbot\OOHost\Emotion;


use Commune\Chatbot\OOHost\Session\Session;

interface Feeling
{

    public function setIntentMap(string $emotionName, array $intentNames) : void;

    public function feel(Session $session, string $emotionName) : ? bool;

    /**
     * @param string $emotionName
     * @param string|callable $experience
     */
    public function experience(string $emotionName, $experience) : void;
}