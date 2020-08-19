<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Data;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Support\Option\AbsOption;


/**
 * 发生 Fallback 的场景.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * # 消息体. 只接受文本.
 * @property-read string $text
 *
 * # 座标
 * @property-read string $sessionId
 * @property-read string $conversationId
 * @property-read string $creatorId
 * @property-read string $creatorName
 * @property-read string $batchId
 *
 * @property-read string $await
 *
 * # 路由状态
 * @property-read string[] $routes
 *
 * # 上下文理解状态
 * @property-read string[] $possibleIntents
 *
 */
class FallbackSceneOption extends AbsOption
{
    const IDENTITY = 'batchId';

    public static function stub(): array
    {
        return [
            'batchId' => '',
            'text' => '',
            'sessionId' => '',
            'conversationId' => '',
            'creatorId' => '',
            'creatorName' => '',
            'await' => '',
            'routes' => [],
            'possibleIntents' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function createFromCloner(Cloner $cloner) : self
    {
        $input = $cloner->input;
        $intention = $cloner->comprehension->intention;
        $message = $input->getMessage();
        $process = $cloner->runtime->getCurrentProcess();
        $prev = $process->prev;

        $routes = isset($prev)
            ? $prev->getAwaitRoutes()
            : [];


        $await = isset($prev) ? $prev->getAwait() : null;
        $await = $await ? $process->getAwait() : null;



        $scene = new FallbackSceneOption([
            'text' => $message->getText(),
            'sessionId' => $cloner->getSessionId(),
            'conversationId' => $cloner->getConversationId(),
            'creatorId' => $input->getCreatorId(),
            'creatorName' => $input->getCreatorName(),
            'batchId' => $input->getBatchId(),
            'await' => isset($await) ? $await->encode() : '',
            'routes' => array_map('strval', $routes),
            'possibleIntents' => $intention->getPossibleIntentNames(),
        ]);

        return $scene;
    }


}