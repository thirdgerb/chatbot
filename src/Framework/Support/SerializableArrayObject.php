<?php

/**
 * Class SerializableArrayObject
 * @package Commune\Chatbot\Framework\Support
 */

namespace Commune\Chatbot\Framework\Support;


use Illuminate\Contracts\Support\Arrayable;

interface SerializableArrayObject extends \ArrayAccess, Arrayable, \JsonSerializable
{

}