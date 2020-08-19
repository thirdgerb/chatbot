<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Pipes;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Components\HeedFallback\Data\FallbackSceneOption;
use Commune\Kernel\ClonePipes\AClonePipe;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @deprecated 暂时考虑不使用这种策略.
 * 因为还没有系统定位消息流的组件.
 * 有系统定位消息流的组件, 只需要在后台查看消息流就可以了, 不用这个策略.
 */
class RecordConvoScenePipe extends AClonePipe
{
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        // 各种情况都不处理.
        if ($request->isStateless()
            || $request->isDelivery()
            || $request->isAsync()
        ) {
            return $next($request);
        }

        $response = $next($request);

        $process = $this->cloner->runtime->getCurrentProcess();
        $prev = $process->prev;
        // 没有上一轮对话的印象. 则不记录了.
        if (!isset($prev)){
            return $response;
        }
        $routes = $process->getAwaitRoutes();


        $input = $request->getInput();
        $message = $input->getMessage();

        $storage = $this->cloner->storage;

        // 本轮对话不是文本, 则去掉记忆.
        if (!$message instanceof VerbalMsg) {
            $storage[FallbackSceneOption::class] = null;
            return $response;
        }

        $scene = new FallbackSceneOption([
            'text' => $message->getText(),
            'conversationId' => $this->cloner->getConversationId(),
            'creatorId' => $input->getCreatorId(),
            'creatorName' => $input->getCreatorName(),
            'batchId' => $input->getBatchId(),
            'fromSession' => $request->getFromSession(),
            'routes' => $routes,
            'possibleIntents' => $this->cloner->comprehension->intention->getPossibleIntentNames(),
            'replies' => [],

        ]);

        $storage[FallbackSceneOption::class] = $scene->toArray();

        return $response;
    }


}