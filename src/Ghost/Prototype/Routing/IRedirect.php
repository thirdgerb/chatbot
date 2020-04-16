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

    public function sleepTo(Context $to = null, string $wakeThreadId = null): Operator
    {
        // TODO: Implement sleepTo() method.
    }

    public function dependOn(Context $depending): Operator
    {
        // TODO: Implement dependOn() method.
    }

    public function block(Context $context): Operator
    {
        // TODO: Implement block() method.
    }

    public function yieldTo(
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
        // TODO: Implement replaceNode() method.
    }

    public function replaceThread(Context $context): Operator
    {
        // TODO: Implement replaceThread() method.
    }

    public function replaceProcess(Context $context): Operator
    {
        // TODO: Implement replaceProcess() method.
    }

    public function home(): Operator
    {
        // TODO: Implement home() method.
    }


}