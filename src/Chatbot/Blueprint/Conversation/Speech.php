<?php

/**
 * Class Monolog
 * @package Commune\Chatbot\Blueprint\Conversation
 */

namespace Commune\Chatbot\Blueprint\Conversation;
use Commune\Chatbot\Framework\Providers\ConversationalServiceProvider;
use Commune\Chatbot\OOHost\HostConversationalServiceProvider;
use Psr\Log\LogLevel;


/**
 * 独白, 用来回复纯文本给用户.
 * 端上可以根据 level 进行一些处理. 比如颜色和提示等.
 */
interface Speech
{
    const DEBUG     = LogLevel::DEBUG;
    const INFO      = LogLevel::INFO;
    const NOTICE    = LogLevel::NOTICE;
    const WARNING   = LogLevel::WARNING;
    const ERROR     = LogLevel::ERROR;


    /**
     * 注册到默认的 slots 给 conversation.
     * @see ConversationalServiceProvider
     */
    const DEFAULT_SLOTS = 'slots.default';  // bind id, 绑定到容器的 id

    const SLOT_USER_NAME = 'default.username'; //用户名
    const SLOT_CHATBOT_NAME = 'default.chatbotName'; //机器人的名称.

    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function debug(string $message, array $slots = []) ;


    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function info(string $message, array $slots = []) ;


    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function warning(string $message, array $slots = []) ;


    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function notice(string $message, array $slots = []) ;


    /**
     * @param string $message
     * @param array $slots
     * @return static
     */
    public function error(string $message, array $slots = []) ;

    public function trans(string $id, array $slots = []) : string;
}