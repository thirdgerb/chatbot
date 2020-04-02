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

use Commune\Message\Prototype\IIntent;
use Commune\Message\Prototype\IJson;
use Commune\Message\Prototype\IText;
use Commune\Message\Prototype\IUnsupported;
use Commune\Message\Prototype\Media\IAudio;
use Commune\Message\Prototype\Media\IImage;
use Commune\Message\Prototype\Media\ILink;
use Commune\Message\Prototype\MessagesTestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DefaultMessageTest extends MessagesTestCase
{
    public function getMessages(): array
    {
        return [
            IUnsupported::class,
            IText::class,
            IIntent::class,
            IJson::class,

            // media
            IImage::class,
            ILink::class,
            IAudio::class

        ];
    }


}