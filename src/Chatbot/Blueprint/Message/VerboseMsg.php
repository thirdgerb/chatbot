<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2019/4/14
 * Time: 6:27 PM
 */

namespace Commune\Chatbot\Blueprint\Message;

use Commune\Chatbot\Blueprint\Conversation\Monologue;
use Commune\Chatbot\Blueprint\Message\Tags\Sendable;
use Commune\Chatbot\Contracts\Translator;

/**
 * 纯文本类型的消息.
 * 可以用于文本或语音.
 * 每一组消息有一个 level.
 * 不同level 的消息, 允许用不同的方式发送
 *
 * Interface Verbose
 * @package Commune\Chatbot\Blueprint\Message
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface VerboseMsg extends Message, Sendable
{
    //默认的消息级别
    const DEBUG = Monologue::DEBUG;
    const INFO = Monologue::INFO;
    const NOTICE = Monologue::NOTICE;
    const WARN = Monologue::WARNING;
    const ERROR = Monologue::ERROR;

    /*--------- 链式调用 ---------*/

    /**
     * 不翻译内容.
     * @return static
     */
    public function raw();

    /**
     * 翻译用的slots
     * @param array $slots
     * @return static
     */
    public function withSlots(array $slots);

    /**
     * 消息的级别.
     * @param string $level
     * @return static
     */
    public function withLevel(string $level);

    /*--------- 获取参数 ---------*/

    public function getLevel() : string;

    public function translate(Translator $translator, string $locale = null) : void;

}