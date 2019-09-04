<?php


namespace Commune\Chatbot\App\Messages\System;


use Commune\Chatbot\Framework\Messages\Reply;

class QuitSessionReply extends Reply
{
    const REPLY_ID = 'system.quit';

    public function __construct()
    {
        parent::__construct(self::REPLY_ID);
    }
}