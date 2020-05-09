<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IActivate;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IDependOn extends AbsDialogue implements Dialog\Activate\DependOn
{
    const SELF_STATUS = self::DEPEND_ON;

    /**
     * @var string
     */
    protected $fieldName;

    public function __construct(Dialog $by, Ucl $on, string $field)
    {
        $this->prev = $by;
        $this->fieldName = $field;
        parent::__construct($by->cloner, $on);
    }

    protected function runInterception(): ? Dialog
    {
        return DialogHelper::intercept($this);
    }

    protected function runTillNext(): Dialog
    {
        return DialogHelper::activate($this);
    }

    protected function selfActivate(): void
    {
        // 先赋值.
        $prev = $this->prev;
        $context = $prev->context;
        $context[$this->fieldName] = $self = $this->context;

        // 添加回调
        $process = $this->getProcess();
        $process->addDepending($prev->ucl, $self->getId());
    }


}