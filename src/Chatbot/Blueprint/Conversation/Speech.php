<?php

/**
 * Class Monolog
 * @package Commune\Chatbot\Blueprint\Conversation
 */

namespace Commune\Chatbot\Blueprint\Conversation;
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


    const DEFAULT_SLOTS = 'slots.default';
    const SLOT_USER_NAME = 'user.name';

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