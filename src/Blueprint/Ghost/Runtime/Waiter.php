<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;

use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 由 Await 生成的等待者, 保留理解对话的现场.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read null|QuestionMsg $question
 * @property-read string[] $stageRoutes
 * @property-read string[] $contextRoutes
 * @property-read string $await
 */
interface Waiter extends ArrayAndJsonAble
{
}