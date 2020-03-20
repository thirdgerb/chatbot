<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Messages\Blueprint\Abstracted;

use Commune\Messages\Blueprint\AbstractedMsg;
use Commune\Messages\Blueprint\Tags\Verbal;

/**
 * 识别消息. 将消息识别成文字.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RecognitionMsg extends AbstractedMsg, Verbal
{

}