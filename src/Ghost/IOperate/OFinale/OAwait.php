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
use Commune\Message\Host\Convo\QA\IChoose;
use Commune\Message\Host\Convo\QA\IConfirm;
use Commune\Message\Host\Convo\QA\IQuestionMsg;
use Commune\Message\Host\Convo\QA\IStepper;
use Commune\Protocals\HostMsg\Convo\QA\Confirm;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use Commune\Protocals\HostMsg\DefaultIntents;
use Commune\Support\Utils\StringUtils;
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
     * @var array
     */
    protected $slots = [];


    /**
     * @var callable[]
     */
    protected $questionCallbacks = [];

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

        $strategy = $contextDef->getStrategy($this->dialog);
        $this->routes = array_merge(
            $this->routes,
            $this->wrapStage($strategy->stageRoutes),
            $this->wrapStage($stageRoutes),
            $this->wrapUcl($strategy->contextRoutes),
            $this->wrapUcl($contextRoutes)
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

    /*----------- ask -----------*/
    public function askStepper(
        string $query,
        int $current,
        int $max,
        array $suggestions = [],
        ?string $next = DefaultIntents::GUEST_LOOP_NEXT,
        ?string $break = DefaultIntents::GUEST_LOOP_BREAK
    ): Operator
    {
        $stepper = IStepper::newStepper(
            $query,
            $current,
            $max
        );

        if (isset($next)) {
            $index = $stepper->addSuggestion($next, null);
            $stepper->nextIndex = $index;
        }

        if (isset($break)) {
            $index = $stepper->addSuggestion($break);
            $stepper->breakIndex = $index;
        }

        $this->question = $this->addSuggestions($stepper, $suggestions);
        return $this;
    }


    public function askChoose(
        string $query,
        array $suggestions = [],
        $defaultChoice = null,
        array $routes = []
    ): Operator
    {
        $choose = IChoose::newChoose($query, $defaultChoice, [], $routes);
        $this->question = $this->addSuggestions($choose, $suggestions);
        return $this;
    }

    public function askConfirm(
        string $query,
        ? bool $default = true,
        $positiveRoute = null,
        $negativeRoute = null,
        string $positive = Confirm::POSITIVE_LANG,
        string $negative = Confirm::NEGATIVE_LANG
    ): Operator
    {
        $confirm = IConfirm::newConfirm($query, $default);
        $positiveRoute = isset($positiveRoute) ? Ucl::decode($positiveRoute) : null;
        $negativeRoute = isset($negativeRoute) ? Ucl::decode($negativeRoute) : null;
        $confirm->setPositive($positive, $positiveRoute);
        $confirm->setNegative($negative, $negativeRoute);

        return $this->ask($confirm);
    }

    public function askVerbal(
        string $query,
        array $suggestions = []
    ): Operator
    {
        $question = IQuestionMsg::instance($query, null);
        $question = $this->addSuggestions($question, $suggestions);
        return $this->ask($question);
    }

    public function ask(
        QuestionMsg $question
    ): Operator
    {
        $question = $question->withSlots($this->slots);
        if (!empty($this->questionCallbacks)) {
            foreach ($this->questionCallbacks as $caller) {
                $caller($question);
            }
        }

        $this->question = $question;

        // process 那一步已经做了这个.
//        $routes = $question->getRoutes();
//        $this->routes = array_merge(
//            $this->routes,
//            array_values($routes)
//        );

        return $this;
    }

    public function withSlots(array $slots): Await
    {
        $this->slots = $slots;
        return $this;
    }

    public function getCurrentQuestion(): ? QuestionMsg
    {
        return $this->question;
    }


    protected function addSuggestions(QuestionMsg $question, array $suggestions) : QuestionMsg
    {
        foreach ($suggestions as $index => $suggestion) {
            $this->addSuggestionToQuestion($question, $index, $suggestion);
        }
        return $question;
    }

    public function addSuggestionToQuestion(QuestionMsg $question, $index, $suggestion) : void
    {
        $success = $this->addUclToQuestion($question, $index, $suggestion)
            || $this->addNormalSuggestion($question, $index, $suggestion);

        if (!$success) {
            $actual = TypeUtils::getType($suggestion);
            throw new InvalidArgumentException(
                "invalid type of question suggestion, only string or Ucl accepted, $actual given"
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
        // $suggestion = Ucl::decode($suggestion);
        // 现在必须手动生成 ucl, 才能避免性能开销和字符串的误判
        // 否则所有和 context 同名的字符串都无法作为一般字符串呈现.
        if (!$suggestion instanceof Ucl) {
            return false;
        }

        $fullname = $suggestion->getStageFullname();
        $isValid = ContextUtils::isValidStageFullName($fullname)
            && $this->getStageReg()->hasDef($fullname);

        if (!$isValid) {
            return false;
        }

        $route = $suggestion;
        if (is_string($index)) {
            $parts = explode('|', $index, 2);
            $index = $parts[0];
            $index = StringUtils::isEmptyStr($index) ? null : $index;
            $suggestion = !empty($parts[1])
                ? $parts[1]
                : $this->getStageReg()->getDef($fullname)->getDescription();

        } else {
            $suggestion = $this->getStageReg()->getDef($fullname)->getDescription();
        }

        $question->addSuggestion($suggestion, $index, $route);
        return true;
    }

    public function withQuestion(callable $caller): Await
    {
        $this->questionCallbacks[] = $caller;
        return $this;
    }


    public function __destruct()
    {
        unset(
            $this->routes,
            $this->slots,
            $this->dialog,
            $this->question,
            $this->stageReg,
            $this->questionCallbacks
        );
        parent::__destruct();
    }
}