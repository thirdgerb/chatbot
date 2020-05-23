<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime\Operators;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Runtime\Finale;
use Commune\Blueprint\Ghost\Runtime\Operator;
use Commune\Message\Host\SystemInt\SessionQuitInt;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloseSession extends AbsOperator implements Finale
{
    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * CloseSession constructor.
     * @param Dialog $dialog
     */
    public function __construct(Dialog $dialog)
    {
        $this->dialog = $dialog;
    }


    protected function toNext(): Operator
    {
        $this->dialog
            ->send()
            ->message(new SessionQuitInt($this->dialog->ucl->toEncodedStr()))
            ->over();

        $this->dialog->cloner->quit();
        return $this;
    }

    public function getOperatorDesc(): string
    {
        return static::class;
    }


}