<?php


namespace Commune\Framework\Blueprint\Conversation;


use Psr\Log\LoggerInterface;

/**
 * 请求级的日志, 所有日志内容都会带上请求相关的参数.
 */
interface ConversationLogger extends LoggerInterface
{

}