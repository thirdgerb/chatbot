<?php


namespace Commune\Chatbot\Blueprint\Message;


/**
 * 将多媒体消息识别为文字消息之后的.
 *
 * media message transform to verbose message
 *
 */
interface RecognitionMsg extends VerboseMsg
{
    public function getMedia() : MediaMsg;
}