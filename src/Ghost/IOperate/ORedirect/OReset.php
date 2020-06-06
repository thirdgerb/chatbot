<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\ORedirect;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IActivate\IReset;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OReset extends AbsRedirect
{

    /**
     * @var Ucl
     */
    protected $root;

    public function __construct(Dialog $dialog, Ucl $reset = null)
    {
        parent::__construct($dialog);

        $this->root = $reset
            ?? $this->dialog->cloner->scene->root;
    }

    protected function toNext(): Operator
    {
        $this->dialog->process->flushWaiting();

        return $this->redirect($this->root, function(Ucl $target) {
            return new IReset($this->dialog, $target);
        });
    }


}