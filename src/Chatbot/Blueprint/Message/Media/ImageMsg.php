<?php


namespace Commune\Chatbot\Blueprint\Message\Media;


use Commune\Chatbot\Blueprint\Message\MediaMsg;

interface ImageMsg extends MediaMsg
{
    /**
     * 图片的url
     * @return string
     */
    public function getUrl() : string;

}