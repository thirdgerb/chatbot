<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;

use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Blueprint\Ghost\Ucl;

/**
 * 定义一个 Context 的依赖参数.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Depending
{

    /**
     * 用 StageMeta 来定义依赖.
     * 最标准的做法.
     * @param string $name
     * @param StageMeta $meta
     * @return Depending
     */
    public function onStage(
        string $name,
        StageMeta $meta
    ) : Depending;

    /**
     * 依赖一个指定的语境.
     *
     * @param string $name
     * @param Ucl $ucl
     * @param string|null $validator
     * @return Depending
     */
    public function onContext(
        string $name,
        Ucl $ucl,
        string $validator = null
    ) : Depending;

    /**
     * 依赖一个指定语境的一个参数.
     *
     * @param string $name
     * @param Ucl $ucl
     * @param string $attrName
     * @param string|null $validator
     * @return Depending
     */
    public function onContextAttr(
        string $name,
        Ucl $ucl,
        string $attrName,
        string $validator = null
    ) : Depending;


    /**
     * 最基本的参数定义. 用一个问题来询问, 接受任何文本值作为结果.
     *
     * @param string $name
     * @param string $query
     * @param string|null $validator
     * @return Depending
     */
    public function on(
        string $name,
        string $query = '',
        string $validator = null
    ) : Depending;

}