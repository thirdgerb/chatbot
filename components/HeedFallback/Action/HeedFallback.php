<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Action;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\NLU\SimpleChat;
use Commune\Components\HeedFallback\Constants\HeedFallbackLang;
use Commune\Components\HeedFallback\Context\LesionTask;
use Commune\Components\HeedFallback\Data\FallbackSceneOption;
use Commune\Components\HeedFallback\Data\StrategyMatcherOption;
use Commune\Components\HeedFallback\HeedFallbackComponent;
use Commune\Components\HeedFallback\Libs\FallbackStrategy;
use Commune\Components\HeedFallback\Libs\FallbackSceneRepository;
use Commune\Components\HeedFallback\Support\HeedFallbackUtils;
use Commune\NLU\Support\NLUUtils;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Support\Registry\Category;
use Commune\Support\Registry\OptRegistry;


/**
 * 触发任务的原理:
 *  - 未命中任何意图
 *  - 命中意图仍然触发了 confuse
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HeedFallback
{
    /*------ config -------*/

//    protected $allowSimpleChat = false;

    /*------ cached -------*/

    /**
     * @var OptRegistry
     */
    protected $registry;

    /**
     * @var Category|null
     */
    protected $strategyCategory;

    /**
     * @var FallbackSceneRepository
     */
    protected $repo;

    /**
     * HeedFallback constructor.
     * @param OptRegistry $registry
     * @param FallbackSceneRepository $repo
     */
    public function __construct(OptRegistry $registry, FallbackSceneRepository $repo)
    {
        $this->registry = $registry;
        $this->repo = $repo;
    }


    /**
     * @param Receive $dialog
     * @return Operator|null
     */
    public function __invoke(Receive $dialog) : ? Operator
    {
        $input = $dialog->input;

        if (!$input->isMsgType(VerbalMsg::class)) {
            return null;
        }

        $text = $input->getMessage()->getText();
        if (NLUUtils::isNotNatureLanguage($text)) {
            return null;
        }

        $cloner = $dialog->cloner;
        $matched = $cloner->comprehension->intention->getMatchedIntent();

        // 如果有 matched intent.
        if (isset($matched)) {
            return $this->onMatchedIntentStrategy($matched, $dialog);

        // 否则走 confuse 流程.
        } else {
            return $this->onConfuseStrategy($dialog);
        }
    }

    protected function onMatchedIntentStrategy(
        string $matchedIntent,
        Receive $dialog
    ) : ? Operator
    {
        $ucl = $dialog->ucl;
        $contextName = $ucl->contextName;
        $stageName = $ucl->stageName;

        $strategy = $this->getStageIntentStrategy($contextName, $stageName, $matchedIntent)
            ?? $this->getContextIntentStrategy($contextName, $matchedIntent)
            ?? $this->getIntentStrategy($matchedIntent);

        // 没有命中的 strategy
        if (empty($strategy)) {
            return $this->onConfuseStrategy($dialog);
        }

        return $this->runStrategy($dialog, $strategy);
    }

    protected function onConfuseStrategy(Receive $dialog) : ? Operator
    {
        $message = $dialog->cloner->input->getMessage();

        // 只能处理 verbal msg
        if (!$message instanceof VerbalMsg) {
            return null;
        }

        // 否则仍然尝试用闲聊模块.
        // 但这个闲聊模块要允许返回 null, 不能全部都硬答.
        $operator = $this->simpleChat($dialog, $message->getText());
        if (isset($operator)) {
            return $operator;
        }

        $cloner = $dialog->cloner;
        // 记录现场.
        $scene = FallbackSceneOption::createFromCloner($cloner);

        // 如果是超管, 直接通知教学好了
        if ($cloner->auth->allow(Supervise::class)) {
            $this->repo->push($scene, false);
            $batchId = $scene->getId();
            return $dialog
                ->send()
                ->notice(HeedFallbackLang::PLEASE_TEACH_ME)
                ->over()
                ->blockTo(LesionTask::genUcl(['batchId' => $batchId]));
        } else {
            $this->repo->push($scene);
        }

        return null;
    }

    /**
     * 简单回复模块.
     * @param Dialog $dialog
     * @param string $text
     * @return Operator|null
     */
    protected function simpleChat(Dialog $dialog, string $text) : ? Operator
    {
        $nlu = $dialog->cloner->nlu;

        if (NLUUtils::isNotNatureLanguage($text)) {
            return null;
        }

        /**
         * @var SimpleChat $chat
         */
        $chat = $nlu->getService($dialog->cloner, SimpleChat::class);
        if (empty($chat)) {
            return null;
        }


        $reply = $chat->reply($text, HeedFallbackComponent::class);

        if (empty($reply)) {
            return null;
        }

        return $dialog
            ->send()
            ->info($reply)
            ->over()
            ->dumb();
    }

    protected function runStrategy(Dialog $dialog, StrategyMatcherOption $option) : ? Operator
    {
        $className = $option->strategyName;
        if (!is_a($className, FallbackStrategy::class, true)) {
            $expect = FallbackStrategy::class;
            $dialog->cloner->logger->error(
                __METHOD__
                . " expect $expect, $className given"
            );
            return null;
        }

        // 用一个 context 来实现回复策略.
        $ucl = call_user_func(
            [$className, FallbackStrategy::FUNC_HANDLER],
            $option->id
        );

        // 默认 block 过去, 可以在 redirect 里更改.
        return $dialog->blockTo($ucl);
    }

    /*------ get category -------*/


    protected function getStrategyCategory() : Category
    {
        return $this->strategyCategory
            ?? $this->strategyCategory = $this
                ->registry
                ->getCategory(StrategyMatcherOption::class);
    }

    /*------ find strategy -------*/


    protected function getStageIntentStrategy(string $context, string $stage, string $intent) : ? StrategyMatcherOption
    {
        $id = HeedFallbackUtils::makeStrategyId($intent, $context, $stage);

        /**
         * @var StrategyMatcherOption $strategy
         */
        $strategy = $this->getStrategyCategory()->has($id)
            ? $this->getStrategyCategory()->find($id)
            : null;
        return $strategy;
    }


    protected function getContextIntentStrategy(string $context, string $intent) : ? StrategyMatcherOption
    {
        $id = HeedFallbackUtils::makeStrategyId($intent, $context);

        /**
         * @var StrategyMatcherOption $strategy
         */
        $strategy = $this->getStrategyCategory()->has($id)
            ? $this->getStrategyCategory()->find($id)
            : null;
        return $strategy;
    }

    protected function getIntentStrategy(string $intent) : ? StrategyMatcherOption
    {
        $id = HeedFallbackUtils::makeStrategyId($intent);

        /**
         * @var StrategyMatcherOption $strategy
         */
        $strategy = $this->getStrategyCategory()->has($id)
            ? $this->getStrategyCategory()->find($id)
            : null;
        return $strategy;
    }

}