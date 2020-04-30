<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Redirect;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Snapshot\Frame;
use Commune\Ghost\Stage\IOnActivateStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DependOn implements Operator
{

    /**
     * @var Frame
     */
    protected $from;

    /**
     * @var Frame
     */
    protected $to;

    public function invoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $process->addDepend($this->from, $this->to);
        $stage = $this->to->findStageDef($cloner);
        return $stage->onStart(new IOnActivateStage(
            $cloner,
            $stage,
            $this->to,
            $this->from
        ));
    }


}