<?php

/**
 * Class PipelineLog
 * @package Commune\Chatbot\Framework\Pipeline
 */

namespace Commune\Chatbot\Framework\Pipeline;

use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Pipeline\ChatbotPipe;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Events\ChatbotPipeClose;
use Commune\Chatbot\Framework\Events\ChatbotPipeStart;

/**
 * Trait PipelineLog
 * @package Commune\Chatbot\Framework\Pipeline
 *
 * @mixin ChatbotPipe
 */
trait PipelineLog
{
    protected function startPipe(Conversation $conversation) : void
    {
        // 运行启动的事件
        $conversation->fire(new ChatbotPipeStart($this));
    }

    protected function endPipe(
        Conversation $conversation,
        Carbon $start = null,
        Carbon $end = null
    ) : void
    {
        if (isset($start) && isset($end)) {
            $pipeName = $this->getPipeName();
            $gap = abs($end->microsecond - $start->microsecond);
            $conversation->getLogger()->info(
                "end chat pipe",
                [
                    'module' => $pipeName,
                    'end' => $end,
                    'gap' => $gap,
                    'memory' => memory_get_usage(true)
                ]
            );
        }

        // 运行结束的事件.
        $conversation->fire(new ChatbotPipeClose($this));
    }


}