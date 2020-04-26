<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Definition;


/**
 * Entity
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EntityDef
{
    /**
     * Context 的名称.
     * @return string
     */
    public function getContextName() : string;

    /**
     * Entity 的名称
     * @return string
     */
    public function getName() : string;

    /**
     * 从 Intention 获得的 Entity 总是数组.
     * @param mixed $value
     * @return mixed
     */
    public function parseEntityVal($value);

    /**
     * @return StageDef
     */
    public function asStage() : StageDef;

}