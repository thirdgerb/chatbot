<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Tree\Strategies;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Components\Tree\Prototype\BranchStageDef;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ATreeActivateStrategy
{

    abstract public function onActivate(Dialog\Activate $dialog) : Operator;

    abstract public function onReceive(Dialog\Receive $dialog) : Operator;

    abstract public function onResume(Dialog\Resume $dialog) : ? Operator;

    public function __invoke(Dialog $dialog) : ? Operator
    {
        if ($dialog instanceof Dialog\Activate) {
            return $this->onActivate($dialog);
        }

        if ($dialog instanceof Dialog\Receive) {
            return $this->onReceive($dialog);
        }

        if ($dialog instanceof Dialog\Resume) {
            return $this->onResume($dialog);
        }

        return null;
    }

    protected function getBranchStageDef(Dialog $dialog) : BranchStageDef
    {
        $def = $dialog->ucl->findStageDef($dialog->cloner);
        TypeUtils::validateInstance($def, BranchStageDef::class, static::class . '::'. __FUNCTION__);
        /**
         * @var BranchStageDef $def
         */
        return $def;
    }

}