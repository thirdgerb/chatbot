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

use Commune\Message\Prototype\Convo\IIntent;
use Commune\Message\Prototype\Convo\IJson;
use Commune\Message\Prototype\Convo\IText;
use Commune\Message\Prototype\Convo\IUnsupported;
use Commune\Message\Prototype\Convo\Media\IAudio;
use Commune\Message\Prototype\Convo\Media\IImage;
use Commune\Message\Prototype\Convo\Media\ILink;
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