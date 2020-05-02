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

use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routing\Retracing;
use Commune\Blueprint\Ghost\Routing\Redirecting;
use Commune\Blueprint\Ghost\Routing\Staging;
use Commune\Blueprint\Ghost\Stage\OnActivate;
use Commune\Ghost\OperatorsBack\End\Await;
use Commune\Ghost\Routing\IRetracing;
use Commune\Ghost\Routing\IRedirecting;
use Commune\Ghost\Routing\IStaging;
use Commune\Protocals\Host\Convo\QuestionMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IOnActivateStage extends AStage implements OnActivate
{
    public function await(QuestionMsg $question = null): Operator
    {
        return new Await($question);
    }

    public function staging(): Staging
    {
        return new IStaging($this);
    }

    public function redirect(): Redirecting
    {
        return new IRedirecting($this);
    }

    public function fallback(): Retracing
    {
        return new IRetracing($this);
    }

}