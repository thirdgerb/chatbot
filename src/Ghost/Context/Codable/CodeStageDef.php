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
        $builder = $this->getStageBuilder($dialog->context);
        return $builder->fire($dialog)
            ?? $this->fireEvent($dialog)
            ?? $dialog->next();
    }

    public function onReceive(Receive $dialog): Operator
    {
        $builder = $this->getStageBuilder($dialog->context);
        return $builder->fire($dialog)
            ?? $this->fireEvent($dialog)
            ?? $dialog->next();
    }

    public function onRedirect(Dialog $prev, Dialog $current): ? Operator
    {
        $builder = $this->getStageBuilder($current->context);
        return $builder->fireRedirect($prev, $current)
            ?? $this->fireRedirect($prev, $current);
    }

    public function onResume(Resume $dialog): ? Operator
    {
        $builder = $this->getStageBuilder($dialog->context);
        return $builder->fire($dialog)
            ?? $this->fireEvent($dialog);
    }

    protected function getMethodName() : string
    {
        return CodeContext::STAGE_BUILDER_PREFIX . $this->getStageShortName();
    }

    protected function getStageBuilder(Context $context) : IStageBuilder
    {
        $builder = new IStageBuilder();
        $creator = [$context, $this->getMethodName()];
        return $creator($builder);
    }
}