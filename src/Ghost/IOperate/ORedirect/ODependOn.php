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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IActivate\IDepend;
use Commune\Ghost\Dialog\IResume\ICallback;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ODependOn extends AbsRedirect
{

    /**
     * @var Ucl
     */
    protected $dependUcl;

    /**
     * @var string|null
     */
    protected $fieldName;

    public function __construct(Dialog $dialog, Ucl $dependUcl, string $fieldName = null)
    {
        $this->dependUcl = $dependUcl;
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
                    $this->dependUcl
                );
        }

        return $this->redirect($this->dependUcl, function (Ucl $ucl) {
            return new IDepend($this->dialog, $ucl);
        });
    }


}