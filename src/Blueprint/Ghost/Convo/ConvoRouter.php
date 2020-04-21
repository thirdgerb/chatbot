<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Convo;

use Commune\Blueprint\Ghost\CloneScope;
use Commune\Protocals\Intercom\GhostInput;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 请求级的单例. 用来转换 clone 的.
 *
 */
interface ConvoRouter
{
    /*------ clone 相关 ------*/

    /**
     * 原始的 GhostInput 输入
     * @return GhostInput
     */
    public function getOriginInput() : GhostInput;

    /**
     * 转换后的 GhostInput
     * @return GhostInput
     */
    public function getInput() : GhostInput;

    /**
     * @return CloneScope
     * @param string|null $cloneId  为 null 获取默认值.
     * @return CloneScope
     */
    public function getCloneScope(string $cloneId = null) : CloneScope;

    /**
     * 设置当前对话的作用域.
     * @param CloneScope $scope
     */
    public function setCloneScope(CloneScope $scope) : void;



}