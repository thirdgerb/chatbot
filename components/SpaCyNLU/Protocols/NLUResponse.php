<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Protocols;

use Commune\Support\Struct\AStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read int $code
 * @property-read string $msg
 * @property-read array|null $proto
 */
class NLUResponse extends AStruct
{
    public static function stub(): array
    {
        return [
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function isSuccess() : bool
    {
        return $this->code === 0;
    }

    public function getCode() : int
    {
        return $this->code;
    }

    public function getMessage() : string
    {
        return $this->msg;
    }

    public function getProtocolData() : array
    {
        return $this->proto ?? [];
    }


}