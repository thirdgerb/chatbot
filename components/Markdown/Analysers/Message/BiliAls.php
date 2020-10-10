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
use Commune\Message\Host\Convo\Media\BiliVideoMsg;
use Commune\Protocols\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BiliAls extends AbsMessageAls
{

    protected function deliverCommentContent(Deliver $deliver, string $content): void
    {
        $iframe = trim($content);
        $message = BiliVideoMsg::instance($iframe, '');
        $deliver->message($message);
    }

    protected function getLevel(): string
    {
        return HostMsg::INFO;
    }


}