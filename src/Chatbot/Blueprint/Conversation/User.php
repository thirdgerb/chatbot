<?php


namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Support\Arr\ArrayAndJsonAble;

interface User extends \ArrayAccess, ArrayAndJsonAble
{

    public function getId() : string;

    public function getName() : string;

    public function getOriginData() : array;

}