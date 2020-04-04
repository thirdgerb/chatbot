<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Kernels;

use Commune\Ghost\Blueprint\Kernels\AsyncKernel;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IAsyncKernel extends AKernel implements AsyncKernel
{

    public function getPipes(): array
    {
    }

    public function onMessage(): bool
    {
        $input = $this->ghost->getMessenger()->popInput();

        if (empty($input)) {
            return false;
        }

        $this->onInput($input);
    }


}