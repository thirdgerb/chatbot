<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Abstracted;

use Commune\Message\Blueprint\Abstracted\CmdStr;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICmdStr implements CmdStr
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    public $cmd;

    public function toArray(): array
    {
        return ['cmd' => $this->cmd];
    }

    public function setCommandStr(string $commandStr): void
    {
        $this->cmd = $commandStr;
    }

    public function getCommandStr(): ? string
    {
        return $this->cmd;
    }
}