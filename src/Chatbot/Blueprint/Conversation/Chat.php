<?php


namespace Commune\Chatbot\Blueprint\Conversation;


interface Chat
{
    public function getUserId() : string;

    public function getChatbotUserName() : string;

    public function getPlatformId() : string;

    public function getChatId() : string;

}