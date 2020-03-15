<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Meta;


/**
 * 元数据的 Wrapper. 接受一个 Meta 数据, 从而具备功能性.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Wrapper
{
    public function __construct(Meta $meta);

    /**
     * 校验 meta 数据是否合法. 不合法的话输出错误信息.
     * @return null|string
     */
    public function validateMeta() : ? string;

    /**
     * 获取原始的 Meta
     * @return Meta
     */
    public function meta() : Meta;
}