<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Session;


/**
 * Session 的公共缓存. 与 SessionId 保持一致.
 * 持久化存储的不需要放在这里.
 * 如果是 stateless 调用则会新创建.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionStorage extends \ArrayAccess
{
    const FIELD_ONCE_NAME = 'onceData';

    public function save() : void;

    /**
     * 设置一次性的数据, 有效期到下一次请求
     * @param array $data
     */
    public function once(array $data) : void;

    /**
     * 获取本次, 或者上次所设置的一次性数据.
     * @return array
     */
    public function getOnce() : array;
}