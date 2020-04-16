<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Staging;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Exceptions\OperatorException;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Prototype\Comprehend\ComprehendPipe;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;

/**
 * Stage 开始倾听当前消息并恢复.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageOnHear extends AbsOperator
{
    /**
     * @var Node
     */
    protected $node;

    /**
     * StageOnHear constructor.
     * @param Node $node
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $stageDef = $this->node->findStageDef($conversation);
            // 运行管道
        return $this->comprehendPipes($stageDef, $conversation)

            // 检查是否命中了 Stage 路由
            ?? $this->stagesRouting($conversation, $stageDef, $this->context)

            // 检查是否命中了 Context 路由
            ?? $this->contextRouting($conversation, $stageDef, $this->context)

            // 都没命中则尝试调用 Heed
            ?? new StageOnHeed(
                $this->stageDef,
                $this->context
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

        // 运行管道.
        if (!empty($pipes)) {
            try {

                $conversation->goThroughPipes(
                    $pipes,
                    ComprehendPipe::HANDLER
                );

                // 允许通过异常中断流程.
            } catch (OperatorException $e) {

                return $e->getOperator();
            }
        }

        return null;
    }


    /**
     * @param Conversation $conversation
     * @param StageDef $stageDef
     * @param Context $context
     * @return Operator|null
     */
    public function stagesRouting(
        Conversation $conversation,
        StageDef $stageDef
    ) : ? Operator
    {
        $stageFullnames = $stageDef->stageRoutes($conversation);

        if (empty($stageFullnames)) {
            return null;
        }

        foreach ($stageFullnames as $stageFullname) {
            $matched = $this->wildcardIntentMatch($stageFullname, $conversation)
                ?? $this->exactIntentMatch($stageFullname, $conversation);

            // 如果匹配到了某个 stage 的名称.
            if (!empty($matched)) {

                $intendingStage = $conversation
                    ->mind
                    ->stageReg()
                    ->getDef($matched);

                return new IntendToStage(
                    $stageDef,
                    $this->node,
                    $intendingStage
                );
            }
        }

        return null;
    }

    public function contextRouting(
        Conversation $conversation,
        StageDef $stageDef,
        Context $context
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
                return null;
            }

            $contextDef = $contextReg->getDef($matched);
            $stageDef = $contextDef->getInitialStageDef();

            $intending = $conversation->runtime->newContext($matched, null);

            return new IntendToContext($stageDef, $context, $intending);
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