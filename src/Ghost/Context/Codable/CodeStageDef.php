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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\Builders\IStageBuilder;
use Commune\Ghost\Stage\AStageDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class CodeStageDef extends AStageDef
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
        $next = $next ?? $this->runStageBuilder($dialog, $dialog->ucl)->popOperator();
        $next = $next ?? $dialog->next();
        return $next;
    }

    public function onReceive(Receive $dialog): Operator
    {
        $next = $this->fireEvent($dialog);
        $next = $next ?? $this->runStageBuilder($dialog, $dialog->ucl)->popOperator() ;
        $next = $next ?? $dialog->confuse();
        return $next;
    }

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return $this->fireRedirect($prev)
            ?? $this->runStageBuilder($prev, $current, true)->popOperator();
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return $this->fireEvent($dialog)
            ?? $this->runStageBuilder($dialog, $dialog->ucl)->popOperator();
    }

    protected function getMethodName() : string
    {
        return Context\CodeContext::STAGE_BUILDER_PREFIX . $this->getStageShortName();
    }

    protected function runStageBuilder(Dialog $dialog, Ucl $current, bool $redirect = false) : IStageBuilder
    {
        $builder = new IStageBuilder($dialog, $this, $redirect);
        $context = $current->findContext($dialog->cloner);

        $func = [$context, $this->getMethodName()];
        $builder = $func($builder);

        return $builder;
    }
}