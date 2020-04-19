<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Tag;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MsgLevel
{
    const ERROR     = 'error';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    const LEVELS = [
        self::ERROR,
        self::NOTICE,
        self::INFO,
        self::DEBUG,
    ];

    public function getLevel() : string;

}