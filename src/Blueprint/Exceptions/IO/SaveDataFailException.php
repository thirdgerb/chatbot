<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Exceptions\IO;

use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Throwable;

/**
 * 保存关键数据异常, 会干扰当前对话逻辑.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SaveDataFailException extends BrokenRequestException
{
    public function __construct(
        string $dataDesc,
        Throwable $e = null
    )
    {
        $message = "save data $dataDesc fail";
        parent::__construct($message, $e);
    }
}