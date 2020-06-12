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
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OAwait extends AbsFinale implements Await
{

    /**
     * @var QuestionMsg|null
     */
    protected $question;

    /**
     * @var Ucl[]
     */
    protected $routes = [];

    /**
     * @var int|null
     */
    protected $expire;

    /**
     * @var Ucl
     */
    protected $current;

    /**
     * @var StageReg
     */
    protected $stageReg;

    /**
     * IAwait constructor.
     * @param Dialog $dialog
     * @param string[]|Ucl[] $stageRoutes
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
        parent::__construct($dialog);

        $this->expire = $expire;
        $this->current = $this->dialog->ucl;
        $contextDef = $this->current->findContextDef($this->cloner);

        // todo routes with stage routes
        $config = $this->cloner->config;
        $this->routes = array_merge(
            $this->routes,
            $this->wrapStage($contextDef->commonStageRoutes()),
            $this->wrapStage($stageRoutes),
            $this->wrapUcl($contextDef->commonContextRoutes()),
            $this->wrapUcl($contextRoutes),
            $this->wrapUcl($config->globalContextRoutes ?? [])
        );
    }

    protected function wrapStage(array $stages) : array
    {
        return array_map(
            function($stage) : Ucl {
                return $stage instanceof Ucl
                    ? $stage
                    : $this->current->goStage($stage);
            },
            $stages
        );
    }

    protected function wrapUcl(array $routes) : array
    {
        return array_map(
            function($route) : Ucl {
                return $route instanceof Ucl
                    ? $route
                    : Ucl::decode($route);
            },
            $routes
        );
    }

    protected function getStageReg() : StageReg
    {
        return $this->stageReg
            ?? $this->stageReg = $this->cloner->mind->stageReg();
    }

    protected function toNext(): Operator
    {
        $this->process->await(
            $this->dialog->ucl,
            $this->question,
            array_unique(array_values($this->routes))
        );

        $this->runAwait();

        if (isset($this->expire)) {
            $this->cloner->setSessionExpire($this->expire);
        }
        return $this;
    }

    public function askChoose(
        string $query,
        array $suggestions = [],
        $defaultChoice = null,
        array $routes = []
    ): Operator
    {
        $choose = new IChoose($query, $defaultChoice, [], $routes);
        $this->question = $this->addSuggestions($choose, $suggestions);
        return $this;
    }

    public function askConfirm(
        string $query,
        ? bool $default = true,
        $positiveRoute = null,
        $negativeRoute = null
    ): Operator
    {
        $confirm = new IConfirm($query, $default);
        $positiveRoute = isset($positiveRoute) ? Ucl::decode($positiveRoute) : null;
        $negativeRoute = isset($negativeRoute) ? Ucl::decode($negativeRoute) : null;
        $confirm->setPositive('yes', $positiveRoute);
        $confirm->setNegative('no', $negativeRoute);

        return $this->ask($confirm);
    }

    public function askVerbal(
        string $query,
        array $suggestions = []
    ): Operator
    {
        $question = new IQuestionMsg($query, null);
        $question = $this->addSuggestions($question, $suggestions);
        return $this->ask($question);
    }

    public function ask(
        QuestionMsg $question
    ): Operator
    {
        $this->question = $question;

        $routes = $question->getRoutes();
        $this->routes = array_merge(
            $this->routes,
            array_values($routes)
        );

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
        $success = $this->addUclToQuestion($question, $index, $suggestion)
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
        if (!TypeUtils::maybeString($suggestion)) {
            return false;
        }

        $suggestion = strval($suggestion);
        $question->addSuggestion($suggestion, $index);
        return true;
    }

    protected function addUclToQuestion(QuestionMsg $question, $index, $suggestion) : bool
    {
        $suggestion = Ucl::decode($suggestion);
        if (!$suggestion instanceof Ucl) {
            return false;
        }


        $fullname = $suggestion->getStageFullname();
        $isValid = ContextUtils::isValidStageFullName($fullname)
            && $this->getStageReg()->hasDef($fullname);

        if (!$isValid) {
            return false;
        }

        $this->routes[] = $suggestion;

        $def = $this->getStageReg()->getDef($fullname);
        $question->addSuggestion($def->getDescription(), $index, $suggestion);

        return true;
    }

    protected function destroy(): void
    {
        unset($this->cloner);
        unset($this->process);
        unset($this->stageReg);
        unset($this->routes);
        unset($this->question);
        unset($this->current);
        unset($this->expire);

        parent::destroy();
    }
}