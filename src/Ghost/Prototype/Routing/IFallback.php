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
        // TODO: Implement reject() method.
    }

    public function cancel(): Operator
    {
        // TODO: Implement cancel() method.
    }

    public function quit(): Operator
    {
        // TODO: Implement quit() method.
    }

    public function fulfill(int $gcTurn = 0): Operator
    {
        // TODO: Implement fulfill() method.
    }


}