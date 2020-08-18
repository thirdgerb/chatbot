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

use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface NLUService
{
    public function getOption() : NLUServiceOption;

    /**
     * 保存一个 meta 数据. 只应该保存和当前相关的.
     * @param DefMeta $meta
     * @return string|null   error message
     */
    public function saveMeta(DefMeta $meta) : ? string;

    /**
     * 同步 Mindset
     * @param Mindset $mind
     * @return string|null  error message
     */
    public function syncMind(Mindset $mind) : ? string;

    /**
     * @param InputMsg $message
     * @param Comprehension $comprehension
     * @return Comprehension
     */
    public function parse(
        InputMsg $message,
        Comprehension $comprehension
    ) : Comprehension;

}