<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Hear;

use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Prototype\Comprehend\ComprehendPipe;
use Commune\Ghost\Prototype\Operators\AbsOperator;
use Commune\Ghost\Prototype\Operators\Breakpoint\End;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsHear extends AbsOperator
{
    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * AbsHear constructor.
     * @param StageDef $stageDef
     */
    public function __construct(StageDef $stageDef)
    {
        $this->stageDef = $stageDef;
    }


    public function routingPipes(Conversation $conversation) : ? Operator
    {
        $pipes = $this->stageDef->comprehendPipes();

        // 经过专属管道.
        if (!empty($pipes)) {
            /**
             * @var GhostConfig $config
             */
            $config = $conversation->getContainer()->get(GhostConfig::class);
            $pipes = $config->comprehendPipes;
        }

        if (!empty($pipes)) {
            $conversation = $conversation->goThroughPipes($pipes, ComprehendPipe::HANDLER);
        }

        // 经过管道如果已经结束了.
        if ($conversation->isFinished()) {
            return new End();
        }

        return null;
    }


    public function routingStages(Conversation $conversation) : ? Operator
    {

    }

    abstract public function routingIntents(Conversation $conversation) : ? Operator;

    abstract public function toChildProcess(Conversation $conversation) : ? Operator;

    abstract public function heard(Conversation $conversation) : Operator;

}