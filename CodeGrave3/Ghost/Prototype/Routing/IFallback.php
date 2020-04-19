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

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Fallback;
use Commune\Ghost\Blueprint\Stage\Stage;
use Commune\Ghost\Prototype\Operators\Current\CancelCurrent;
use Commune\Ghost\Prototype\Operators\Current\FulfillCurrent;
use Commune\Ghost\Prototype\Operators\Current\QuitCurrent;
use Commune\Ghost\Prototype\Operators\Current\RejectCurrent;
use Commune\Message\Blueprint\Message;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IFallback implements Fallback
{
    /**
     * @var Stage
     */
    protected $stage;

    /**
     * IFallback constructor.
     * @param Stage $stage
     */
    public function __construct(Stage $stage)
    {
        $this->stage = $stage;
    }


    public function reject(Message $message = null): Operator
    {
        return new RejectCurrent();
    }

    public function cancel(): Operator
    {
        return new CancelCurrent();
    }

    public function quit(): Operator
    {
        return new QuitCurrent();
    }

    public function fulfill(int $gcTurn = 0): Operator
    {
        return new FulfillCurrent($gcTurn);
    }


}