<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Current;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Comprehend\ComprehendPipe;
use Commune\Ghost\Operators\Intend\IntendToContext;
use Commune\Ghost\Operators\Staging\StageOnHeed;
use Commune\Ghost\Stage\IOnHeedStage;

/**
 * 用当前 Process 的 aliveThread 去处理输入消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HearStage implements Operator
{


    public function invoke(Cloner $cloner): ? Operator
    {
        $node = $cloner->runtime->getCurrentProcess()->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($cloner);

        // 运行管道
        return $this->comprehendPipes($stageDef, $cloner)

            // 检查是否命中了 Stage 路由
            ?? $this->stagesRouting($cloner, $stageDef, $node)

            // 检查是否命中了 Context 路由
            ?? $this->contextRouting($cloner, $stageDef, $node)

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
     * @param Conversation $cloner
     * @return Operator|null
     */
    public function comprehendPipes(StageDef $stageDef, Conversation $cloner) : ? Operator
    {
        // 检查当前 Stage 是否有自定义的管道.
        $pipes = $stageDef->comprehendPipes($cloner);

        // 没有自定义管道, 则用公共管道.
        if (!empty($pipes)) {
            /**
             * @var GhostConfig $config
             */
            $config = $cloner->getContainer()->get(GhostConfig::class);
            $pipes = $config->comprehendPipes;
        }

        // 运行理解管道.
        if (!empty($pipes)) {
            $cloner->goThroughPipes(
                $pipes,
                ComprehendPipe::HANDLER
            );
        }

        return null;
    }


    /**
     * 检查 Stage 的路由.
     * @param Conversation $cloner
     * @param StageDef $stageDef
     * @param Node $node
     * @return Operator|null
     */
    public function stagesRouting(
        Conversation $cloner,
        StageDef $stageDef,
        Node $node
    ) : ? Operator
    {
        $stageNames = $stageDef->stageRoutes($cloner);

        // 如果没有 stage, 不需要去路由.
        if (empty($stageNames)) {
            return null;
        }

        foreach ($stageNames as $stageFullname) {

            // 进行精确匹配和模糊匹配.
            $matched = $this->wildcardIntentMatch($stageFullname, $cloner)
                ?? $this->exactIntentMatch($stageFullname, $cloner);

            // 如果匹配到了某个 stage 的名称.
            if (!empty($matched)) {

                // 获取重定向的 Stage
                $intendingStage = $cloner
                    ->mind
                    ->stageReg()
                    ->getDef($matched);

                // 命中 Stage 的话, 直接执行 heed 方法.
                $heed = new IOnHeedStage(
                    $cloner,
                    $intendingStage,
                    $node
                );
                return $intendingStage->onHeed($heed);
            }
        }

        return null;
    }

    public function contextRouting(
        Conversation $cloner,
        StageDef $stageDef,
        Node $node
    ) : ? Operator
    {
        $contextNames = $stageDef->contextRoutes($cloner);

        if (empty($contextNames)) {
            return null;
        }

        $contextReg = $cloner->mind->contextReg();

        foreach ($contextNames as $contextName) {
            $matched = $this->wildcardIntentMatch($contextName, $cloner)
                ?? $this->exactIntentMatch($contextName, $cloner);

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
            $intendingNode = $cloner->newContext($matched)->toNewNode();

            return new IntendToContext(
                $stageDef,
                $node,
                $intendingNode
            );
        }

        return null;
    }


    protected function wildcardIntentMatch(string $intent, Conversation $cloner) : ? string
    {
        $intention = $cloner->ghostInput->comprehension->intention;
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

    protected function exactIntentMatch(string $intent, Conversation $cloner) : ? string
    {
        $intentReg = $cloner->mind->intentReg();
        if (! $intentReg->hasDef($intent)) {
            return null;
        }

        // 用意图单元来匹配.
        $intentDef = $intentReg->getDef($intent);
        $matched = $intentDef->validate($cloner);

        if ($matched) {
            $cloner
                ->ghostInput
                ->comprehension
                ->intention
                ->setMatchedIntent($intent);
        }

        return $matched ? $intent : null;
    }



}