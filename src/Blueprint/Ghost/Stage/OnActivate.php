<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Stage;

use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routing\Fallback;
use Commune\Blueprint\Ghost\Routing\Redirect;
use Commune\Blueprint\Ghost\Routing\Staging;
use Commune\Protocals\Host\Convo\QuestionMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 更多属性
 * @see Stage
 */
interface OnActivate extends Stage
{

    /**
     * 等待用户回复.
     *
     * @param QuestionMsg|null $question
     * @return Operator
     */
    public function await(
        QuestionMsg $question = null
    ) : Operator;

    public function staging() : Staging;

    public function redirect() : Redirect;

    public function fallback() : Fallback;

}