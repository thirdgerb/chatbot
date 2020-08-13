<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $dependedUcl
 * @property-read string|null $dependedAttr
 * @property-read string|null $validator
 */
class DependStageDef extends AStageDef
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'contextName' => '',
            'title' => '',
            'desc' => '',

            'dependedUcl' => '',
            'dependedAttr' => null,
            'validator' => null,

            'stageName' => '',
            'asIntent' => [],
            'events' => [],
            'ifRedirect' => null,
        ];
    }

    public function onActivate(Activate $dialog): Operator
    {
        $name = $this->getStageShortName();
        $value = $dialog->context[$name];
        if (
            isset($value)
            && $value instanceof Context
            && $value->isFulfilled()
        ) {
            return $dialog->next();
        }

        $ucl = Ucl::decode($this->dependedUcl);
        $attr = $this->dependedAttr;

        return $dialog->dependOn(
            $ucl,
            $attr
        );
    }

    public function onReceive(Receive $dialog): Operator
    {
        $ucl = Ucl::decode($this->dependedUcl);
        $attr = $this->dependedAttr;
        $name = $this->getStageShortName();

        $depended = $ucl->findContext($dialog->cloner);

        if (!$depended->isFulfilled()) {
            return $dialog->reactivate();
        }

        if (isset($attr)) {
            $dialog->context[$name] = $depended->offsetGet($attr);
        } else {
            $dialog->context[$name] = $depended;
        }

        return $dialog->next();
    }

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return null;
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return $dialog->reactivate();
    }


}