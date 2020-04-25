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

use Commune\Blueprint\Ghost\Context\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Operators\Events\ToActivateStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ReplaceProcess implements Operator
{
    /**
     * @var Context|null
     */
    protected $context;

    /**
     * ReplaceProcess constructor.
     * @param Context|null $context
     */
    public function __construct(Context $context = null)
    {
        $this->context = $context;
    }

    public function invoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $process->home($this->context);

        $node = $process->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($cloner);

        return new ToActivateStage($stageDef, $node);
    }


}