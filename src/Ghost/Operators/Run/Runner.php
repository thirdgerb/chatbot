<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Run;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Snapshot\Process;
use Commune\Protocals\Intercom\GhostInput;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class Runner implements Operator
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * @var GhostInput
     */
    protected $input;

    /**
     * Runner constructor.
     * @param Process $process
     * @param GhostInput $input
     */
    public function __construct(Process $process, GhostInput $input)
    {
        $this->process = $process;
        $this->input = $input;
    }



    protected function wildcardIntentMatch(string $intent, Cloner $cloner) : ? string
    {
        $intention = $cloner->ghostInput->comprehension->intention;
        // 不是模糊搜索就退出.
        if (!$intention->isWildcardIntent($intent)) {
            return null;
        }

        $matched = $intention->wildcardIntentMatch($intent);
        if (empty($matched)) {
            return null;
        }

        $intention->setMatchedIntent($matched);
        return $matched;
    }

    protected function exactIntentMatch(string $intent, Cloner $cloner) : ? string
    {
        $intentReg = $cloner->mind->intentReg();
        if (! $intentReg->hasDef($intent)) {
            return null;
        }

        // 用意图单元来匹配.
        $intentDef = $intentReg->getDef($intent);
        $matched = $intentDef->validate($cloner);

        if ($matched) {
            $cloner
                ->ghostInput
                ->comprehension
                ->intention
                ->setMatchedIntent($intent);
        }

        return $matched ? $intent : null;
    }

}