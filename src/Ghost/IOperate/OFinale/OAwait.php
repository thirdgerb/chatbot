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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindReg\StageReg;
use Commune\Blueprint\Ghost\Operate\Await;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Support\ContextUtils;
use Commune\Message\Host\QA\IChoose;
use Commune\Message\Host\QA\IConfirm;
use Commune\Message\Host\QA\IQuestionMsg;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use Commune\Support\Utils\TypeUtils;

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
     * @var Ucl
     */
    protected $ucl;

    /**
     * @var StageReg
     */
    protected $stageReg;

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

        $this->ucl = $this->dialog->ucl;
        $this->stageReg = $this->cloner->mind->stageReg();
    }


    protected function toNext(): Operator
    {
        $def = $this->dialog->ucl->findContextDef($this->cloner);

        $this->process->await(
            $this->dialog->ucl,
            $this->question,
            $this->getStageRoutes($def),
            $this->getContextRoutes($def)
        );

        $this->runAwait();

        if (isset($this->expire)) {
            $this->cloner->setSessionExpire($this->expire);
        }

        return $this;
    }

    protected function getStageRoutes(ContextDef $contextDef) : array
    {
        return array_unique(
            array_merge(
                $this->stageRoutes,
                $contextDef->commonStageRoutes()
            )
        );
    }

    protected function getContextRoutes(ContextDef $contextDef) : array
    {
        return array_unique(
            array_merge(
                $this->contextRoutes,
                $contextDef->commonContextRoutes(),
                $this->cloner->config->globalContextRoutes
            )
        );
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
        $positiveRoute = null,
        $negativeRoute = null
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

    public function ask(
        QuestionMsg $question
    ): Operator
    {
        $this->question = $question;
        return $this;
    }

    protected function addSuggestions(QuestionMsg $question, array $suggestions) : QuestionMsg
    {
        foreach ($suggestions as $index => $suggestion) {
            $this->addSuggestionToQuestion($question, $index, $suggestion);
        }

        return $question;
    }

    protected function addSuggestionToQuestion(QuestionMsg $question, $index, $suggestion) : void
    {
        $success = $this->addStageSuggestion($question, $index, $suggestion)
            || $this->addUclToQuestion($question, $index, $suggestion)
            || $this->addContextRouteToQuestion($question, $index, $suggestion)
            || $this->addNormalSuggestion($question, $index, $suggestion);

        if (!$success) {
            throw new InvalidArgumentException(
                "invalid type of question suggestion, only string or Ucl accepted"
            );
        }
    }

    protected function addNormalSuggestion(
        QuestionMsg $question,
        $index,
        $suggestion
    ) : bool
    {
        if (!TypeUtils::isString($suggestion)) {
            return false;
        }

        $suggestion = strval($suggestion);
        $question->addSuggestion($suggestion, $index);
        return true;
    }

    protected function addContextRouteToQuestion(
        QuestionMsg $question,
        $index,
        $suggestion
    ) : bool
    {
        if (!is_string($suggestion)) {
            return false;
        }

        $ucl = Ucl::decodeUclStr($suggestion);
        return $this->addUclToQuestion($question, $index, $ucl);
    }



    protected function addUclToQuestion(QuestionMsg $question, $index, $suggestion) : bool
    {
        if (!$suggestion instanceof Ucl) {
            return false;
        }

        $fullname = $suggestion->getStageFullname();
        if (ContextUtils::isValidStageFullName($fullname) || !$this->stageReg->hasDef($fullname)) {
            return false;
        }

        $this->contextRoutes[] = $uclStr = $suggestion->toEncodedStr();
        $question->addSuggestion($question, $index, $fullname);

        return true;
    }

    protected function addStageSuggestion(QuestionMsg $question, $index, $suggestion) : bool
    {
        if (!is_string($suggestion) || !ContextUtils::isValidStageName($suggestion)) {
            return false;
        }

        $stageName = $suggestion;

        $fullname = $this->ucl->getStageFullname($stageName);
        if (!$this->stageReg->hasDef($fullname)) {
            return false;
        }

        $def = $this->stageReg->getDef($fullname);
        $this->stageRoutes[] = $stageName;
        $question->addSuggestion($def->getDescription(), $index, $fullname);

        return true;
    }


}