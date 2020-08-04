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

use Commune\Blueprint\Ghost\Context\Dependable;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IActivate\IDepend;
use Commune\Ghost\IOperate\Flows\FallbackFlow;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ODependOn extends AbsRedirect
{

    /**
     * @var Dependable
     */
    protected $dependable;

    /**
     * @var string|null
     */
    protected $fieldName;

    public function __construct(Dialog $dialog, Dependable $dependable, string $fieldName = null)
    {
        $this->dependable = $dependable;
        $this->fieldName = $fieldName;
        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        if (isset($this->fieldName)) {
            $this->dialog
                ->context
                ->offsetSet(
                    $this->fieldName,
                    $this->dependable
                );
        }

        // 如果依赖对象已经完成了, 则直接走 callback
        if ($this->dependable->isFulfilled()) {
            $ucl = $this->dialog->ucl;
            $this->dialog->process->addCallback($ucl);
            return new FallbackFlow($this->dialog);
        }

        // 添加依赖栈
        $this->dialog->process->addDepending(
            $this->dialog->ucl,
            $this->dependable->toFulfillUcl()->getContextId()
        );

        // 否则重定向.
        return $this->redirect(
            $this->dependable->toFulfillUcl(),
            function (Ucl $ucl) {
                return new IDepend($this->dialog, $ucl);
            }
        );
    }


}