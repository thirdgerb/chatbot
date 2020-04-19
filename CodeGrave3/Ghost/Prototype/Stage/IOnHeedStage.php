<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Stage;

use Commune\Ghost\Blueprint\Callables\Operating;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Backward;
use Commune\Ghost\Blueprint\Routing\Fallback;
use Commune\Ghost\Blueprint\Routing\Hearing;
use Commune\Ghost\Blueprint\Routing\Staging;
use Commune\Ghost\Blueprint\Stage\OnHeed;
use Commune\Ghost\Prototype\Operators\End\NoStateEnd;
use Commune\Ghost\Prototype\Routing\IBackward;
use Commune\Ghost\Prototype\Routing\IFallback;
use Commune\Ghost\Prototype\Routing\IHearing;
use Commune\Ghost\Prototype\Routing\IStaging;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IOnHeedStage extends AStage implements OnHeed
{
    public function end(): Operator
    {
        return $this->hearing()->end();
    }

    public function privateEnd(): Operator
    {
        return $this->hearing()->privateEnd();
    }

    public function dumb(): Operator
    {
        return new NoStateEnd();
    }

    public function confuse(): Operator
    {
        // TODO: Implement confuse() method.
    }

    public function hearing(): Hearing
    {
        return new IHearing($this);
    }

    public function staging(): Staging
    {
        return new IStaging($this);
    }

    public function fallback(): Fallback
    {
        return new IFallback($this);
    }

    public function backward(): Backward
    {
        return new IBackward();
    }


}