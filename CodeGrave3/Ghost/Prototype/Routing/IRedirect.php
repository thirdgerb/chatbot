<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Routing;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Redirect;
use Commune\Ghost\Blueprint\Stage\Stage;
use Commune\Ghost\Prototype\Operators\Redirect\DependOn;
use Commune\Ghost\Prototype\Operators\Redirect\ReplaceNode;
use Commune\Ghost\Prototype\Operators\Redirect\ReplaceProcess;
use Commune\Ghost\Prototype\Operators\Redirect\ReplaceThread;
use Commune\Ghost\Prototype\Operators\Redirect\SleepTo;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRedirect implements Redirect
{
    /**
     * @var Stage
     */
    protected $stage;

    /**
     * IRedirect constructor.
     * @param Stage $stage
     */
    public function __construct(Stage $stage)
    {
        $this->stage = $stage;
    }

    public function sleepTo(Context $to = null, string $wakeThreadId = null, int $gcTurn = 0): Operator
    {
        if (isset($to) && !$to->isInstanced()) {
            $to = $to->toInstance($this->stage->conversation);
        }

        return new SleepTo($to, $wakeThreadId, $gcTurn);
    }

    public function dependOn(Context $depending): Operator
    {
        if (!$depending->isInstanced()) {
            $depending = $depending->toInstance($this->stage->conversation);
        }

        return new DependOn($depending);
    }

    public function yieldTo(
        string $shellName,
        string $shellId,
        Context $asyncContext,
        Context $toContext = null,
        string $wakeThreadId = null,
        int $expire = null
    ): Operator
    {
        // TODO: Implement yieldTo() method.
    }

    public function replaceNode(Context $context): Operator
    {
        if (!$context->isInstanced()) {
            $context = $context->toInstance($this->stage->conversation);
        }

        return new ReplaceNode($context);
    }

    public function replaceThread(Context $context): Operator
    {
        if (!$context->isInstanced()) {
            $context = $context->toInstance($this->stage->conversation);
        }

        return new ReplaceThread($context);
    }

    public function replaceProcess(Context $context): Operator
    {
        if (!$context->isInstanced()) {
            $context = $context->toInstance($this->stage->conversation);
        }

        return new ReplaceProcess($context);
    }

    public function home(): Operator
    {
        return new ReplaceProcess(null);
    }


}