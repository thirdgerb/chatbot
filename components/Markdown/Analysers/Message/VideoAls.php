<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Analysers\Message;

use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Message\Host\Convo\Media\IVideoMsg;
use Commune\Protocals\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class VideoAls extends AbsMessageAls
{
    protected function deliverCommentContent(Deliver $deliver, string $content): void
    {
        $data = explode(' ', $content, 2);
        $resource = trim($data[0]);

        if (isset($data[1])) {
            $text = trim($data[1]);
        } else {
            $text = null;
        }

        $deliver->message(IVideoMsg::instance($resource, $text, $this->getLevel()));
    }

    protected function getLevel(): string
    {
        return HostMsg::DEBUG;
    }


}