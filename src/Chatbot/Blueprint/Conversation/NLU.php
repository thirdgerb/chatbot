<?php


namespace Commune\Chatbot\Blueprint\Conversation;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Support\Arr\ArrayAndJsonAble;
use Illuminate\Support\Collection;

/**
 * Conversation 的自然语言识别单元
 *
 * NLU protocol
 * to set or get nlu information of incoming message
 *
 */
interface NLU extends ArrayAndJsonAble
{

    /**
     * 标记 NLU 已经完成过信息获取. 并记录是谁获取的.
     * mark nlu has be handled
     *
     * @param string|null $nluId
     */
    public function done(string $nluId = null) : void;


    /**
     * NLU的数据已经处理过.
     * 通常有多个NLU的情况下, 也只处理一次.
     * 返回处理者的ID
     *
     * @return null|string
     */
    public function isHandledBy() : ? string;

    /*----- matched intent -----*/

    /**
     * 获取已匹配到的intent
     *
     * @return null|string
     */
    public function getMatchedIntent() : ? string;

    /**
     * 设置一个匹配到的intent
     *
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
     * 全局的entity. 有些 NLU 的 entity 和 intent 是分开匹配的.
     * @return Collection of entities map
     */
    public function getGlobalEntities() : Collection;

    /**
     * 获取 全局entities + 命中意图的 entities
     * @return Collection of global and matched intent entities
     */
    public function getMatchedEntities() : Collection;

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