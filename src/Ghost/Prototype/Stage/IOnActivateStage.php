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

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Fallback;
use Commune\Ghost\Blueprint\Routing\Redirect;
use Commune\Ghost\Blueprint\Routing\Staging;
use Commune\Ghost\Blueprint\Stage\OnActivate;
use Commune\Ghost\Prototype\Operators\End\Await;
use Commune\Ghost\Prototype\Routing\IFallback;
use Commune\Ghost\Prototype\Routing\IRedirect;
use Commune\Ghost\Prototype\Routing\IStaging;
use Commune\Message\Blueprint\QuestionMsg;

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