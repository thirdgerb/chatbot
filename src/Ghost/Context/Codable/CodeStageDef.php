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
        $next = $this->fireEvent($dialog);
        $next = $next ?? $this->runStageBuilder($dialog)->popOperator();
        $next = $next ?? $dialog->next();
        return $next;
    }

    public function onReceive(Receive $dialog): Operator
    {
        $next = $this->fireEvent($dialog);
        $next = $next ?? $this->runStageBuilder($dialog)->popOperator() ;
        $next = $next ?? $dialog->next();
        return $next;
    }

    public function onRedirect(Dialog $prev, Dialog $current): ? Operator
    {
        return $this->fireRedirect($prev, $current)
            ?? $this->runStageBuilder($current, true)->popOperator();
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return $this->fireEvent($dialog)
            ?? $this->runStageBuilder($dialog)->popOperator();
    }

    protected function getMethodName() : string
    {
        return Context\CodeContext::STAGE_BUILDER_PREFIX . $this->getStageShortName();
    }

    protected function runStageBuilder(Dialog $dialog, bool $redirect = false) : IStageBuilder
    {
        $builder = new IStageBuilder($dialog, $redirect);
        $context = $dialog->context;

        $func = [$context, $this->getMethodName()];
        $builder = $func($builder);

        return $builder;
    }
}