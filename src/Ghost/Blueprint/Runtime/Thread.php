<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Runtime;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Message\Blueprint\QuestionMsg;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id  Thread 的唯一ID, 由 Root 决定
 * @property-read int $priority 当前 Thread 的优先级
 * @property-read int $depth Thread 的深度
 *
 */
interface Thread extends ArrayAndJsonAble
{

    /**
     * 当前节点
     * @return Node
     */
    public function currentNode() : Node;

    public function replaceNode(Node $node) : void;

    /**
     * 前进一个 Node
     * @param Node $node
     */
    public function pushNode(Node $node) : void;

    /**
     * 后退一个节点, 并把当前节点当成一个新的 Thread 抛出.
     * @return Node|null
     */
    public function popNode() : ? Node;

    /*--------- more information ---------*/

    public function getDescription(Conversation $conversation) : string;

    /*--------- question ---------*/

    public function getQuestion() : ? QuestionMsg;

    public function setQuestion(QuestionMsg $questionMsg) : void;

    /*--------- gc ---------*/

    public function setGc(int $turns);

    /**
     * 尝试回收 Thread
     * @return bool
     */
    public function gc() : bool;

}