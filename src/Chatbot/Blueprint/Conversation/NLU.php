<?php


namespace Commune\Chatbot\Blueprint\Conversation;

use Commune\Chatbot\Blueprint\Message\Message;
use Illuminate\Support\Collection;

/**
 * NLU protocol
 *
 * to set or get nlu information of incoming message
 *
 */
interface NLU
{

    public function done() : void;

    public function isHandled() : bool;

    /*----- matched intent -----*/

    /**
     * @return null|string
     */
    public function getMatchedIntent() : ? string;

    /**
     * @param string $intentName
     */
    public function setMatchedIntent(string $intentName) : void;

    /*----- possible intent -----*/


    /**
     * @param string $intentName
     * @param int $odd
     * @param bool $highlyPossible
     * @return mixed
     */
    public function addPossibleIntent(string $intentName, int $odd, bool $highlyPossible = true);

    /**
     * @return Collection of list(string $intentName, int $odd, bool $highlyPossible)
     */
    public function getPossibleIntents() : Collection;

    /**
     * @return null|string
     */
    public function getMostPossibleIntent() : ? string;

    /**
     * @param string $intentName
     * @param bool $highlyOnly
     * @return bool
     */
    public function hasPossibleIntent(string $intentName, bool $highlyOnly = true) : bool;

    /**
     * @param bool $highlyOnly
     * @return string[]
     */
    public function getPossibleIntentNames(bool $highlyOnly = true) : array;

    /**
     * @param string $intentName
     * @return int|null
     */
    public function getOddOfPossibleIntent(string $intentName) : ? int;

    /*----- entities -----*/

    /**
     * @param array $entities
     */
    public function setEntities(array $entities) : void;

    /**
     * @param string $intentName
     * @param array $entities
     */
    public function setIntentEntities(string $intentName, array $entities) : void;

    /**
     * @return Collection of entities map
     */
    public function getEntities() : Collection;

    /**
     * @param string $intentName
     * @return Collection
     */
    public function getIntentEntities(string $intentName) : Collection;

    /*----- extra -----*/

    /**
     * @return Collection of Messages.
     */
    public function getDefaultReplies() : Collection;

    /**
     * @param Message $message
     */
    public function addDefaultReply(Message $message) : void;


    /*----- emotion -----*/

    /**
     * @return Collection  of string
     */
    public function getEmotions() : Collection;

    /**
     * @param string $emotionName
     */
    public function addEmotion(string $emotionName) : void;

    /**
     * @param string[] $emotionNames
     */
    public function setEmotions(array $emotionNames) : void;

    /*----- words 分词 -----*/


    /**
     * @param array $words
     */
    public function setWords(array $words) : void;

    /**
     * @return Collection
     */
    public function getWords() : Collection;

    /*----- focus -----*/

    /**
     * NLU 知道当前 stage 要注意哪些 intent
     * @param string $intentName
     */
    public function focusIntent(string $intentName) : void;

    /**
     * 获取 nlu 所有注意中的intent
     * @return Collection
     */
    public function getFocusIntents() : Collection;
}