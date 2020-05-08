<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;

/**
 * 单轮对话结束时, 对下一轮对话进行主动响应的路由图.
 * 上下文会根据路由的情况, 选择进入指定的 ucl 进行响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string[] $watching            监视类路由. 会触发 Watch 事件
 * [ string $ucl ]
 *
 * @property-read string[] $stageRoutes         分支路由, 会触发 Staging 事件
 * [ string $stageName ]
 *
 * @property-read string[] $contextRoutes       意图路由, 会触发 Intend 事件.
 *
 * @property-read string $heed                  默认的 Heed 对应的 ucl
 *
 * @property-read string[] $wake                唤醒路由, 会触发 Wake 事件
 *
 * @property-read string[] $restore             复活路由, 会触发 Rebirth 事件.
 *
 */
interface RoutesMap
{
}