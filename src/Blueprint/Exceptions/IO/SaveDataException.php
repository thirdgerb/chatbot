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

/**
 * 保存关键数据异常, 会干扰当前对话逻辑.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SaveDataException extends DataIOException
{
}