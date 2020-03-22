<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;

use Commune\Ghost\Blueprint\Runtime\Node;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class INode implements Node
{
    /**
     * 所处 Context 的 Id
     * @var string
     */
    protected $contextId;

    /**
     * 所处 Context 的名称
     * @var string
     */
    protected $contextName;

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * stage 的名称
     * @var string
     */
    protected $stageName;

    /**
     * 接下来要经过的 stage 名称
     * @var string[]
     */
    protected $stacks = [];


}