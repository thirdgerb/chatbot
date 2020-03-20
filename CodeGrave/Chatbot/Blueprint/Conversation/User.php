<?php


namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 从请求中获取的用户信息
 */
interface User extends \ArrayAccess, ArrayAndJsonAble
{

    public function getId() : string;

    public function getName() : string;

    public function getOriginData() : array;

}