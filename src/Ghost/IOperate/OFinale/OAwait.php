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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Message\Host\QA\IChoose;
use Commune\Message\Host\QA\IConfirm;
use Commune\Message\Host\QA\IQuestionMsg;
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

    public function askChoose(
        string $query,
        array $suggestions = [],
        $defaultChoice = null
    ): Operator
    {
        $choose = new IChoose($query, $defaultChoice);
        $this->question = $this->addSuggestions($choose, $suggestions);
        return $this;
    }

    public function askConfirm(
        string $query,
        bool $default = true,
        Ucl $positiveRoute = null,
        Ucl $negativeRoute = null
    ): Operator
    {
        $confirm = new IConfirm($query, $default);
        $confirm->setPositive('yes', $positiveRoute);
        $confirm->setNegative('no', $negativeRoute);
        return $this;
    }

    public function askVerbal(
        string $query,
        array $suggestions = []
    ): Operator
    {
        $question = new IQuestionMsg($query, null);
        $this->question = $this->addSuggestions($question, $suggestions);
        return $this;
    }

    protected function addSuggestions(QuestionMsg $question, array $suggestions) : QuestionMsg
    {
        foreach ($suggestions as $index => $suggestion) {
            if ($this->isCurrentStage($suggestion)) {
                $question = $this->addStageToQuestion($question, $index, $suggestion);

            } elseif ($suggestion instanceof Ucl) {
                $question = $this->addUclToQuestion($question, $index, $suggestion);

            } elseif ($this->isContextName($suggestion)) {
                $question = $this->addContextNameToQuestion($question, $index, $suggestion);
            } else {
                $question->addSuggestion($suggestion, $index);
            }
        }

        return $question;
    }

    public function ask(
        QuestionMsg $question
    ): Operator
    {
        $this->question = $question;
        return $this;
    }


}