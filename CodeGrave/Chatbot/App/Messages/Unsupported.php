<?php


namespace Commune\Chatbot\App\Messages;

use Commune\Chatbot\Blueprint\Message\UnsupportedMsg;
use Commune\Chatbot\Framework\Messages\AbsConvoMsg;

/**
 * 不支持的消息类型用的替身.
 */
class Unsupported extends AbsConvoMsg implements UnsupportedMsg
{

    public function isEmpty(): bool
    {
        return true;
    }

    public function getText(): string
    {
        return '';
    }

    public static function mock()
    {
        return new static();
    }


}