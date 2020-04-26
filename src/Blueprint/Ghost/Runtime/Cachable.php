<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;


/**
 * Runtime 中需要缓存的对象.
 * Runtime 的缓存最好能同时 expire.
 *
 * 都用序列化来保存.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Cachable
{
    /**
     * @return bool
     */
    public function isCaching() : bool;

    /**
     * 是否要删掉缓存.
     * @return bool
     */
    public function isExpired() : bool;

    /**
     * 缓存的时候用的 ID
     * @return string
     */
    public function getCachableId() : string;

}