<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AStruct extends AbstractStruct
{
    const RELATIONS = [];

    public function getId()
    {
        return static::class;
    }

    abstract public static function stub(): array;

    abstract public static function validate(array $data): ? string; /* errorMsg */
    abstract public static function relations(): array;



}