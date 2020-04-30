<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Snapshot;

use Commune\Protocals\Host\Convo\QuestionMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *  *
 * ## before
 * @property-read string[] $yielding
 * @property-read int[] $blocking
 * @property-read string[][] $watching
 *
 * ## await
 * @property-read QuestionMsg|null $question
 * @property-read string[] $stageRoutes
 * @property-read string[] $contextRoutes
 * @property-read string $heed
 *
 * ## after
 * @property-read string[][] $sleeping
 * @property-read string[][] $gc
 */
interface ReactsMap
{

}