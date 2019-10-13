<?php


namespace Commune\Chatbot\Blueprint\Message;

use Commune\Chatbot\Blueprint\Message\Media\AudioMsg;
use Commune\Chatbot\Blueprint\Message\Tags\Transformed;

/**
 * 将多媒体消息识别为文字消息之后的.
 *
 * media message transform to verbose message
 *
 */
interface RecognitionMsg extends VerboseMsg, Transformed
{
    public function getMedia() : AudioMsg;
}