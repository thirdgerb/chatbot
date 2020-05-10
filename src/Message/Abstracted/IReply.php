<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Abstracted;

use Commune\Protocals\Abstracted\Reply;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property HostMsg[] $replies
 */
class IReply extends AbsMessage implements Reply
{
    protected $transferNoEmptyRelations = false;

    public static function stub(): array
    {
        return [
            'replies' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'replies[]' => HostMsg::class,
        ];
    }

    public function isEmpty(): bool
    {
        return empty($this->_data['replies']);
    }

    public function addMessage(HostMsg $message): void
    {
        $this->_data['replies'][] = $message;
    }

    public function getMessages(): array
    {
        return $this->replies;
    }


}