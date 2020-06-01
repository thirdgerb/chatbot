<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OFinale;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Await;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OAwait extends AbsFinale implements Await
{

    /**
     * @var string[]
     */
    protected $stageRoutes;

    /**
     * @var QuestionMsg|null
     */
    protected $question;

    /**
     * @var string[]
     */
    protected $contextRoutes;

    /**
     * @var int|null
     */
    protected $expire;

    /**
     * IAwait constructor.
     * @param Dialog $dialog
     * @param array $stageRoutes
     * @param array $contextRoutes
     * @param int $expire
     */
    public function __construct(
        Dialog $dialog,
        array $stageRoutes,
        array $contextRoutes,
        ? int $expire
    )
    {
        $this->dialog = $dialog;
        $this->stageRoutes = $stageRoutes;
        $this->contextRoutes = $contextRoutes;
        $this->expire = $expire;
        parent::__construct($dialog);
    }


    protected function toNext(): Operator
    {
        $this->process->await(
            $this->dialog->ucl,
            $this->question,
            $this->stageRoutes,
            $this->contextRoutes
        );
        return $this;
    }


}