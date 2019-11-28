<?php


namespace Commune\Chatbot\Blueprint\Conversation;


/**
 * 用于定义一种权限.
 */
interface Ability
{

    public function isAllowing(Conversation $conversation) : bool ;

}