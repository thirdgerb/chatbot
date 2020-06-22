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
use Commune\Ghost\Dialog\IActivate\IStaging;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OGoStage extends AbsRedirect
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
        $stages = $this->stages;
        $first = array_shift($stages);
        $target = $this->dialog->ucl->goStage($first);

        return $this->redirect($target, function($target){
            return new IStaging($this->dialog, $target);
        }, $stages);
    }


}