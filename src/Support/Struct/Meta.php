<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id            meta 的 Id
 * @property-read string $wrapper       meta 的包装类对象
 * @property-read array $config         meta 的原始数据
 */
interface Meta extends Struct
{
    /**
     * Meta 的类型, 通常是类名
     * @return string
     */
    public function getMetaType() : string;

    /**
     * 将自身封装的 $config 封装成一个 wrapper 对象.
     * 不需要依赖注入.
     *
     * @return Wrapper
     */
    public function wrap() : Wrapper;

}