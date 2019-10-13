<?php


namespace Commune\Chatbot\Blueprint\Message\Replies;


use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Blueprint\Message\Tags\NoTranslate;
use Commune\Chatbot\Framework\Conversation\ConversationImpl;


/**
 * 段落类型的消息. 可以把多个消息的 text 合并成一段.
 *
 * @see DialogSpeech  beginParagraph endParagraph
 * @see ConversationImpl
 */
interface Paragraph extends ReplyMsg, NoTranslate
{
    /**
     * 添加一个 reply
     * @param ReplyMsg $reply
     * @return Paragraph
     */
    public function add(ReplyMsg $reply) : Paragraph;

    /**
     * 获取所有的reply
     * @return ReplyMsg[]
     */
    public function getReplies() : array ;

    /**
     * reply 被render后, 将文字重新加入.
     * @param string[] $texts
     * @return Paragraph
     */
    public function withText(string ...$texts) : Paragraph;

}