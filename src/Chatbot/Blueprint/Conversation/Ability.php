<?php


namespace Commune\Chatbot\Blueprint\Conversation;


interface Ability
{

    public function isAllowing(Conversation $conversation) : bool ;

}