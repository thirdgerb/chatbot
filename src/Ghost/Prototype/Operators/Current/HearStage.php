<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Current;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Prototype\Comprehend\ComprehendPipe;
use Commune\Ghost\Prototype\Operators\Intend\IntendToContext;
use Commune\Ghost\Prototype\Operators\Staging\StageOnHeed;
use Commune\Ghost\Prototype\Stage\IOnHeedStage;

/**
 * 用当前 Process 的 aliveThread 去处理输入消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HearStage implements Operator
{


    public function invoke(Conversation $conversation): ? Operator
    {
        $node = $conversation->runtime->getCurrentProcess()->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($conversation);

        // 运行管道
        return $this->comprehendPipes($stageDef, $conversation)

            // 检查是否命中了 Stage 路由
            ?? $this->stagesRouting($conversation, $stageDef, $node)

            // 检查是否命中了 Context 路由
            ?? $this->contextRouting($conversation, $stageDef, $node)

            // 都没命中则尝试调用 Heed
            ?? new StageOnHeed(
                $stageDef,
                $node
            );
    }

    /**
     * 尝试理解输入消息.
     *
     * @param StageDef $stageDef
     * @param Conversation $conversation
     * @return Operator|null
     */
    public function comprehendPipes(StageDef $stageDef, Conversation $conversation) : ? Operator
    {
        // 检查当前 Stage 是否有自定义的管道.
        $pipes = $stageDef->comprehendPipes($conversation);

        // 没有自定义管道, 则用公共管道.
        if (!empty($pipes)) {
            /**
             * @var GhostConfig $config
             */
            $config = $conversation->getContainer()->get(GhostConfig::class);
            $pipes = $config->comprehendPipes;
        }

        // 运行理解管道.
        if (!empty($pipes)) {
            $conversation->goThroughPipes(
                $pipes,
                ComprehendPipe::HANDLER
            );
        }

        return null;
    }


    /**
     * 检查 Stage 的路由.
     * @param Conversation $conversation
     * @param StageDef $stageDef
     * @param Node $node
     * @return Operator|null
     */
    public function stagesRouting(
        Conversation $conversation,
        StageDef $stageDef,
        Node $node
    ) : ? Operator
    {
        $stageNames = $stageDef->stageRoutes($conversation);

        // 如果没有 stage, 不需要去路由.
        if (empty($stageNames)) {
            return null;
        }

        foreach ($stageNames as $stageFullname) {

            // 进行精确匹配和模糊匹配.
            $matched = $this->wildcardIntentMatch($stageFullname, $conversation)
                ?? $this->exactIntentMatch($stageFullname, $conversation);

            // 如果匹配到了某个 stage 的名称.
            if (!empty($matched)) {

                // 获取重定向的 Stage
                $intendingStage = $conversation
                    ->mind
                    ->stageReg()
                    ->getDef($matched);

                // 命中 Stage 的话, 直接执行 heed 方法.
                $heed = new IOnHeedStage(
                    $conversation,
                    $intendingStage,
                    $node
                );
                return $intendingStage->onHeed($heed);
            }
        }

        return null;
    }

    public function contextRouting(
        Conversation $conversation,
        StageDef $stageDef,
        Node $node
    ) : ? Operator
    {
        $contextNames = $stageDef->contextRoutes($conversation);

        if (empty($contextNames)) {
            return null;
        }

        $contextReg = $conversation->mind->contextReg();

        foreach ($contextNames as $contextName) {
            $matched = $this->wildcardIntentMatch($contextName, $conversation)
                ?? $this->exactIntentMatch($contextName, $conversation);

            if (empty($matched)) {
                continue;
            }

            if (!$contextReg->hasDef($matched)) {
                continue;
            }

            $contextDef = $contextReg->getDef($matched);
            if (!$contextDef->isPublic()) {
                continue;
            }

            $stageDef = $contextDef->getInitialStageDef();
            $intendingNode = $conversation->newContext($matched)->toNewNode();

            return new IntendToContext(
                $stageDef,
                $node,
                $intendingNode
            );
        }

        return null;
    }


    protected function wildcardIntentMatch(string $intent, Conversation $conversation) : ? string
    {
        $intention = $conversation->ghostInput->comprehension->intention;
        // 如果是模糊搜索
        if (!$intention->isWildcardIntent($intent)) {
            return null;
        }

        $matched = $intention->wildcardIntentMatch($intent);

        if (empty($matched)) {
            return null;
        }

        $intention->setMatchedIntent($matched);
        return $matched;
    }

    protected function exactIntentMatch(string $intent, Conversation $conversation) : ? string
    {
        $intentReg = $conversation->mind->intentReg();
        if (! $intentReg->hasDef($intent)) {
            return null;
        }

        // 用意图单元来匹配.
        $intentDef = $intentReg->getDef($intent);
        $matched = $intentDef->validate($conversation);

        if ($matched) {
            $conversation
                ->ghostInput
                ->comprehension
                ->intention
                ->setMatchedIntent($intent);
        }

        return $matched ? $intent : null;
    }



}