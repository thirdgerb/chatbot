<?php


namespace Commune\Chatbot\App\Messages\System;


use Commune\Chatbot\Blueprint\Conversation\Renderer;
use Commune\Chatbot\Framework\Messages\AbsReply;

class QuitSessionReply extends AbsReply
{

    public function __construct()
    {
        parent::__construct(Renderer::QUIT_ID);
    }

    public static function mock()
    {
        return new static();
    }
}