<?php


namespace Commune\Chatbot\Blueprint\Conversation;


interface User extends \ArrayAccess
{

    public function getId() : string;

    public function getName() : string;

    public function getOriginData() : array;

}