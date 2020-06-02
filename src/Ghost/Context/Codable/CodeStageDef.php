<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Codable;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\Builders\IStageBuilder;
use Commune\Ghost\Stage\AbsStageDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class CodeStageDef extends AbsStageDef
{


    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',

            'contextName' => '',
            'stageName' => '',
            'asIntent' => [],

            'events' => [],
            'ifRedirect' => null,
        ];
    }

    /*------ callable ------*/

    public function onActivate(Activate $dialog): Operator
    {
        return $this->fireEvent($dialog)
            ?? $this->getStageBuilder($dialog)->operator
            ?? $dialog->next();
    }

    public function onReceive(Receive $dialog): Operator
    {
        return $this->fireEvent($dialog)
            ?? $this->getStageBuilder($dialog)->operator
            ?? $dialog->next();
    }

    public function onRedirect(Dialog $prev, Dialog $current): ? Operator
    {
        return $this->fireRedirect($prev, $current)
            ?? $this->getStageBuilder($current, true)->operator;
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return $this->fireEvent($dialog)
            ?? $this->getStageBuilder($dialog)->operator;
    }

    protected function getMethodName() : string
    {
        return Context\CodeContext::STAGE_BUILDER_PREFIX . $this->getStageShortName();
    }

    protected function getStageBuilder(Dialog $dialog, bool $redirect = false) : IStageBuilder
    {
        $builder = new IStageBuilder($dialog, $redirect);
        $context = $dialog->context;
        return  call_user_func([$context, $this->getMethodName()], $builder);
    }
}