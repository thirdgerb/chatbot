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
use Commune\Ghost\IOperate\OExiting\OFulfill;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ONext extends AbsRedirect
{
    /**
     * @var string|null
     */
    protected $orNext;

    public function __construct(Dialog $dialog, string $orNext = null)
    {
        $this->orNext = $orNext;
        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $next = $this->dialog->task->popPath()
            ?? $this->dialog->ucl->goStage($this->orNext);

        if (empty($next)) {
            return new OFulfill($this->dialog);
        }

        if ($next->stageName === $this->dialog->ucl->stageName) {
            return $this->dialog->reactivate();
        }

        $activate = new IStaging($this->dialog, $next);
        return $this->activate($activate, []);
    }


}