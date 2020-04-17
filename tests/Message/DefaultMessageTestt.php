<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Message;

use Commune\Message\Predefined\IIntentMsg;
use Commune\Message\Predefined\IJson;
use Commune\Message\Predefined\IText;
use Commune\Message\Predefined\IUnsupported;
use Commune\Message\Predefined\Media\IAudio;
use Commune\Message\Predefined\Media\IImage;
use Commune\Message\Predefined\Media\ILink;
use Commune\Message\Predefined\MessagesTestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DefaultMessageTestt extends MessagesTestCase
{
    public function getMessages(): array
    {
        return [
            IUnsupported::class,
            IText::class,
            IIntentMsg::class,
            IJson::class,

            // media
            IImage::class,
            ILink::class,
            IAudio::class

        ];
    }


}