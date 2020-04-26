<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Ghost;

use Commune\Blueprint\Ghost\Runtime\Cachable;
use Commune\Blueprint\Ghost\Runtime\Savable;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RuntimeDriver
{

    /*------ cachable ------*/

    /**
     * 缓存所有可以缓存的对象.
     * @param Cachable[] $cachable
     * @return bool
     */
    public function cache(array $cachable) : bool;

    /**
     * 清除掉 Cachable 对象, 只需要 Id 就可以了.
     * @param string[] $cachableIds
     * @return bool
     */
    public function expireCachable(array $cachableIds) : bool;

    /**
     * @param string $cachableId
     * @return Cachable|null
     */
    public function fetchCachable(string $cachableId) : ? Cachable;

    /**
     * @param string[] $cachableIds
     * @return Cachable[]  [ $id => $cachable, ]
     */
    public function fetchCachableMap(array $cachableIds) : array;

    /**
     * @param string $cachableId
     * @return bool
     */
    public function hasCachable(string $cachableId) : bool;


    /*------ savable ------*/

    /**
     * 保存长期对象.
     * @param Savable[] $savable
     * @return bool
     */
    public function save(array $savable) : bool;

}