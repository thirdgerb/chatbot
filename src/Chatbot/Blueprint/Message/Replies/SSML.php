<?php


namespace Commune\Chatbot\Blueprint\Message\Replies;


use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Blueprint\Message\Tags\NoTranslate;

/**
 * SSML 类型的消息
 */
interface SSML extends ReplyMsg, NoTranslate
{
    const TAG_L = '{{';
    const TAG_R = '}}';

    /**
     * 标签文本. 子标签用占位符 {{id}} 来表示.
     * @return string
     */
    public function getContent() : string;

    public function getFormatted() : string;

    public function getTag() : string;

    public function getAttrs() : array;

    public function getAttr(string $name);

    public function setAttr(string $name, $value);

    /**
     * 自标签
     * @return SSML[]
     */
    public function getSubSSMLs() : array;

}