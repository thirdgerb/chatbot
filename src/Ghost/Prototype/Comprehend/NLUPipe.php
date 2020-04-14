<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Comprehend;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\NLU\NLUService;

/**
 * 自然语言理解单元.
 * 使用系统默认的 NLUService 进行理解.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NLUPipe extends ComprehendPipe
{
    /**
     * 默认的 NLUService 的 abstract
     * 修改它则可以使用别的 abstract 作为依赖注入的对象.
     *
     * @var string
     */
    protected $nluServiceAbstract = NLUService::class;

    /**
     * @param Conversation $conversation
     * @param callable $next
     * @return Conversation
     */
    public function handle(
        Conversation $conversation,
        callable $next
    ): Conversation
    {
        $ghostInput = $conversation->ghostInput;
        $comprehension = $ghostInput->comprehension;
        $matched = $comprehension->intent->getMatchedIntent();

        if (!empty($matched)) {
            return $next($conversation);
        }

        // text 可以来源于 recognition 之类的.
        $text = $conversation->ghostInput->getTrimmedText();

        // 如果为空
        if (empty($text)) {
            return $next($conversation);
        }


        // 获取服务
        $service = $this->getNLUService($conversation);

        // 检查是否要进行 nlu 筛检. 一些数据比如 数字, 单字母等, 没必要放到 nlu 里.
        if ($service->shouldComprehend($text)) {
            $comprehension = $service->comprehend($comprehension, $text);
            $ghostInput->comprehension = $comprehension;
        }

        return $next($conversation);
    }

    public function getNLUService(Conversation $conversation) : NLUService
    {
        return $conversation->getContainer()->make($this->nluServiceAbstract);
    }


}