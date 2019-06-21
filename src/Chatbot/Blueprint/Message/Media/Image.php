<?php


namespace Commune\Chatbot\Blueprint\Message\Media;


use Commune\Chatbot\Blueprint\Message\MediaMsg;

interface Image extends MediaMsg
{
    /**
     * 图片的url
     * @return string
     */
    public function getUrl() : string;

}