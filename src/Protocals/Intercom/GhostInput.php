<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Intercom;

use Commune\Blueprint\Ghost\CloneScope;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 默认属性
 * @see GhostMsg
 *
 * # 请求的 shell 相关信息
 * @property-read string $shellName
 * @property-read string $shellId
 * @property-read string $sceneId
 * @property-read array $env
 *
 * # 抽象
 * @property-read Comprehension $comprehension  传递过来的语境理解.
 */
interface GhostInput extends GhostMsg
{

    /**
     * 替换当前输入信息对应的分身.
     *
     * @param CloneScope $scope
     */
    public function replaceScope(CloneScope $scope) : void;
}