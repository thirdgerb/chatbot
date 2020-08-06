<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindDef\Registers;

use Commune\Blueprint\Ghost\MindMeta\EmotionMeta;
use Commune\Blueprint\Ghost\MindSelfRegister;
use Commune\Blueprint\Ghost\Mindset;


/**
 * 用类来注册 Emotion 的做法.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class EmotionRegister implements MindSelfRegister
{

    abstract public static function getMeta() : EmotionMeta;

    public static function selfRegisterToMind(Mindset $mindset, bool $force = false): void
    {
        $meta = static::getMeta();
        $mindset->emotionReg()->registerDef($meta->toWrapper());
    }


}