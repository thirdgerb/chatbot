<?php


namespace Commune\Chatbot\Blueprint\Message;


interface RecognitionMsg extends VerboseMsg
{
    public function getMedia() : MediaMsg;
}