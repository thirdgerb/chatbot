<?php


namespace Commune\Chatbot\App\Messages\System;


use Commune\Chatbot\Framework\Messages\AbsReply;

class QuitSessionReply extends AbsReply
{
    const REPLY_ID = 'system.quit';

    public function __construct()
    {
        parent::__construct(self::REPLY_ID);
    }

    public static function mock()
    {
        return new static();
    }
}