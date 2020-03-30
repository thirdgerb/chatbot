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

use Commune\Message\Blueprint\Abstracted\Tokenization;
use Commune\Support\Arr\ArrayAbleToJson;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ITokenization implements Tokenization
{
    use ArrayAbleToJson;

    public $tokens = [];

    public function toArray(): array
    {
        return [
            'tokens' => $this->tokens
        ];
    }

    public function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }


}