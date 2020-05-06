<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Memory;

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 可以被记忆的对象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Memorable extends ArrayAndJsonAble
{
    public function toStub() : Stub;

    /**
     * 合并数据
     * @param array $data
     */
    public function mergeData(array $data): void;

    /**
     * 重置数据.
     * @param array $data
     */
    public function resetData(array $data): void;


    /**
     * 获取 Data 的原始值.
     * @return array
     */
    public function toData() : array;

}