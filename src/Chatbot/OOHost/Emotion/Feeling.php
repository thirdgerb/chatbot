<?php


namespace Commune\Chatbot\OOHost\Emotion;


use Commune\Chatbot\OOHost\Session\Session;

interface Feeling
{

    /**
     * 用 emotion => intentName[] 的数组, 来界定那些intent 属于哪些 emotion
     *
     * define curtain intents means the given emotion
     *
     * @param string $emotionName
     * @param array $intentNames
     */
    public function setIntentMap(string $emotionName, array $intentNames) : void;

    /**
     * feel if session incoming message match given emotion
     *
     * by registered 'experience' callers;
     *
     * @param Session $session
     * @param string $emotionName
     * @return bool|null
     */
    public function feel(Session $session, string $emotionName) : ? bool;

    /**
     * register a 'experience' caller to determine message match given emotion
     *
     * @param string $emotionName
     * @param string|callable $experience  accept session argument and return bool value as prediction
     */
    public function experience(string $emotionName, callable $experience) : void;
}