<?php


namespace Commune\Chatbot\App\Messages\Replies;

use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Framework\Messages\AbsReply;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Illuminate\Support\Collection;

class Reply extends AbsReply implements ReplyMsg
{

    public static function mock()
    {
        return new static('replyId', new Collection(['a' => 1, 'b' => 2]), Speech::ERROR);
    }
}