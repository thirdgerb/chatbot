<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Intercom;

use Commune\Framework\Blueprint\Abstracted\Comprehension;
use Commune\Message\Blueprint\IntentMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $messageId
 * @property-read string $chatId
 * @property-read string $shellName
 * @property-read ShellInput $shellMessage
 *
 * @property-read string $sceneId
 * @property-read array $sceneEnv
 * @property-read Comprehension $comprehension
 *
 */
interface GhostInput extends GhostMsg
{
    public function getTrimmedText() : string;

    public function getMatchedIntent() : ? IntentMsg;
}