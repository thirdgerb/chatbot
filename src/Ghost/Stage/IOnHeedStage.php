<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Callables\Operating;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routing\Hearing;
use Commune\Blueprint\Ghost\Routing\Fallbacking;
use Commune\Blueprint\Ghost\Routing\Hearing;
use Commune\Blueprint\Ghost\Routing\Staging;
use Commune\Blueprint\Ghost\Stage\OnHeed;
use Commune\Ghost\OperatorsBack\End\NoStateEnd;
use Commune\Ghost\Routing\IHearing;
use Commune\Ghost\Routing\IFallbacking;
use Commune\Ghost\Routing\IHearing;
use Commune\Ghost\Routing\IStaging;


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

    public function fallback(): Fallbacking
    {
        return new IFallbacking($this);
    }

    public function backward(): Hearing
    {
        return new IHearing();
    }


}