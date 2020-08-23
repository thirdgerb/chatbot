<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\NLU;

use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface NLUService
{

    /**
     * 同步 Mindset
     * @param Mindset $mind
     * @return string|null  error message
     */
    public function syncMind(Mindset $mind) : ? string;

    /**
     * @param InputMsg $input
     * @param Session $session
     * @param Comprehension $comprehension
     * @return Comprehension
     */
    public function parse(
        InputMsg $input,
        Session $session,
        Comprehension $comprehension
    ) : Comprehension;


    public function saveMeta(Cloner $cloner, DefMeta $meta) : ? string ;
}