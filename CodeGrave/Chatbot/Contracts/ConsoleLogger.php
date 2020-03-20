<?php


namespace Commune\Chatbot\Contracts;


use Psr\Log\LoggerInterface;

/**
 * 系统专用的 console 日志.
 * 启动时使用. 同时也输出致命错误到 console
 */
interface ConsoleLogger extends LoggerInterface
{
}