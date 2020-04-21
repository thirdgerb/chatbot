<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Message;

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * PHP 通用传输消息的设计. 有以下几个特点:
 *
 * 1. 可以转化为数组, 然后用对数组序列化的方式进行传输.
 * 1. 数组本身有通用格式, 见 Transfer. 是跨端一致的
 * 1. 可以从 Transfer 数组还原为 Message 对象.
 * 1. 数据可以作为协议来使用, 并且服务 PHP 的强类型定义
 * 1. 一个 Message 可以实现多个协议, 类似 Interface, 协议也是可以嵌套的.
 * 1. 关联关系自动生成.
 *
 *
 * 消息序列化与反序列化:
 *
 *  $message->toTransfer()->toArray();
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Message extends ProtocalInstance, ArrayAndJsonAble
{
    const RELATIONS = [
        // 'fieldName' => Message::class,
        // 'fieldName[]' => Message::class,
    ];

    /**
     * Message 的默认值.
     * @return array
     */
    public static function stub() : array;

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data) : Message;

    /**
     * 作为传输对象的 ID
     * @return string
     */
    public static function getTransferType() : string;

    /**
     * 校验一个数组是否是合法的协议数组. 返回字符串来标记第一条错误信息
     * @param array $data
     * @return null|string
     */
    public static function validate(array $data) : ? string /* errorMsg */;

    /**
     * 转化为一个传输对象.
     * @return Transfer
     */
    public function toTransfer() : Transfer;

}