<?php


namespace Commune\Chatbot\Blueprint\Message\Transformed;

use Commune\Chatbot\Blueprint\Message\Media\AudioMsg;
use Commune\Chatbot\Blueprint\Message\TransformedMsg;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;

/**
 * 将多媒体消息识别为文字消息之后的.
 *
 * media message transform to verbose message
 *
 */
interface RecognitionMsg extends VerbalMsg, TransformedMsg
{
    public function getMedia() : AudioMsg;
}