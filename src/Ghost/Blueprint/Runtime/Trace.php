<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Runtime;


/**
 * 对话场景切换的状态. 
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextId 运行时所处的 ContextId
 * @property-read string $contextName 运行时所处的 ContextName
 * @property-read string $stageName 运行时所处的 Stage
 * @property-read string $stageEvent 运行时的 StageEvent
 */
interface Trace
{
    public function deep() : int;

    public function prev() : ? Trace;

    public function root() : Trace;

    public function prevContextTrace() : ? Trace;

    public function prevStageTrace() : ? Trace;
}