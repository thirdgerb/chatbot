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
use Commune\Blueprint\Ghost\Routing\Fallback;
use Commune\Blueprint\Ghost\Routing\Redirect;
use Commune\Blueprint\Ghost\Routing\Staging;
use Commune\Blueprint\Ghost\Stage\OnActivate;
use Commune\Ghost\Operators\End\Await;
use Commune\Ghost\Routing\IFallback;
use Commune\Ghost\Routing\IRedirect;
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

    public function redirect(): Redirect
    {
        return new IRedirect($this);
    }

    public function fallback(): Fallback
    {
        return new IFallback($this);
    }

}