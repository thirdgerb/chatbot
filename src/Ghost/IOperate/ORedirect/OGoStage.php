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
use Commune\Ghost\IOperate\AbsOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OGoStage extends AbsOperator
{

    /**
     * @var array
     */
    protected $stages;

    public function __construct(Dialog $dialog, array $stages)
    {
        $this->stages = $stages;
        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $this->dialog->task->insertPaths($this->stages);
        return $this->dialog->next();
    }


}