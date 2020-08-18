<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\TNTSearch\Platform;

use Swoole\Coroutine\Server\Connection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TNTProtocal
{
    const CODE_CLASSIFY_PREDICT = 11;
    const CODE_CLASSIFY_LEARN = 12;
    const CODE_CLASSIFY_SAVE_ALL = 13;
    const CODE_CLASSIFY_FLUSH = 14;


    public function run(Connection $conn, string $data) : void
    {
        $decoded = json_decode($data, true);


    }

    public function output(int $code, string $data) : void
    {

    }
}