<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo\QA;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Protocals\HostMsg\Convo\EventMsg;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocals\HostMsg\Convo\QA\Step;
use Commune\Protocals\HostMsg\Convo\QA\Stepper;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\HostMsg\DefaultEvents;
use Commune\Protocals\HostMsg\DefaultIntents;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property int $currentStep
 * @property int $maxStep
 * @property mixed|null $breakIndex
 * @property mixed|null $nextIndex
 */
class IStepper extends IQuestionMsg implements Stepper
{
    const MODE = self::MATCH_INDEX
        | self::MATCH_SUGGESTION
        | self::MATCH_INTENT
        | self::MATCH_ANY;

    public static function newStepper(
        string $query,
        int $current,
        int $max,
        array $suggestions = [],
        array $routes = [],
        $nextIndex = null,
        $breakIndex = null
    ) : IStepper
    {
        $question = static::instance(
            $query,
            null,
            $suggestions,
            $routes
        );

        $question->currentStep = $current > $max
            ? $max
            : $current;

        $default = $max > $current
            ? $current + 1
            : $max;
        $question->default = $default;
        $question->maxStep = $max;
        $question->nextIndex = $nextIndex;
        $question->breakIndex = $breakIndex;
        return $question;
    }

    public static function stub(): array
    {
        return [
            'query' => '',
            'currentStep' => 0,
            'maxStep' => 0,
            'breakIndex' => null,
            'nextIndex' => null,
            'suggestions' => [],
            'routes' => [],
            'default' => null,
            'translated' => false,
            'mode' => self::MODE,
        ];
    }


    protected function parseAnswerByType(InputMsg $inputMsg) : ? AnswerMsg
    {
        $message = $inputMsg->getMessage();
        if (
            $message instanceof EventMsg
            && $message->getEventName() === DefaultEvents::EVENT_CLIENT_ACKNOWLEDGE
        ) {
            return $this->newStep($this->currentStep + 1);
        }
        return null;
    }

    protected function acceptAnyVerbalAnswer(VerbalMsg $message): ? AnswerMsg
    {
        $current = $this->currentStep + 1;
        return $this->newStep($current);
    }

    protected function newStep(int $step) : Step
    {
        if ($step === $this->maxStep && isset($this->breakIndex)) {
            $choice = $this->breakIndex;
        } elseif ($step === ($this->currentStep + 1) && isset($this->nextIndex)) {
            $choice = $this->nextIndex;
        } else {
            $choice = null;
        }

        $answer = isset($choice)
            ? ($this->suggestions[$choice] ?? '')
            : '';

        $route = isset($choice)
            ? ($this->routes[$choice] ?? null)
            : null;

        return new IStep([
            'answer' => $answer,
            'choice' => $choice,
            'route' => $route,
            'max' => $this->maxStep,
            'current' => $step,
        ]);
    }

    protected function makeAnswerInstance(
        string $answer,
        $choice = null,
        string $route = null
    ): AnswerMsg
    {
        $current = $this->getCurrentStepFromChoice($choice);
        return new IStep([
            'answer' => $answer,
            'choice' => $choice,
            'route' => $route,
            'max' => $this->maxStep,
            'current' => $current
        ]);
    }

    protected function getCurrentStepFromChoice($choice) : int
    {
        if (isset($choice)) {
            if ($choice === $this->breakIndex) {
                return $this->maxStep;
            } elseif ($choice === $this->nextIndex) {
                return $this->currentStep + 1;
            }
        }

        return $this->currentStep;
    }



    protected function parseAnswerByMatcher(Cloner $cloner) : ? AnswerMsg
    {
        $matcher = $cloner->matcher->refresh();
        $break = DefaultIntents::GUEST_LOOP_BREAK;
        $next = DefaultIntents::GUEST_LOOP_NEXT;
        $prev = DefaultIntents::GUEST_LOOP_PREVIOUS;
        $rewind = DefaultIntents::GUEST_LOOP_REWIND;

        if ($matcher->refresh()->isIntent($rewind)->truly()) {
            return $this->newStep(0);
        }

        if ($matcher->refresh()->isIntent($prev)->truly()) {
            $step = $this->currentStep - 1;
            $step = $step > 0 ? $step : 0;
            return $this->newStep($step);
        }

        if ($matcher->refresh()->isIntent($next)->truly()) {
            $step = $this->currentStep + 1;
            return $this->newStep($step);
        }

        if ($matcher->refresh()->isIntent($break)->truly()) {
            return $this->newStep($this->maxStep);
        }

        return null;
    }

    public function getCurrentStep(): int
    {
        return $this->currentStep;
    }

    public function getMaxStep(): int
    {
        return $this->maxStep;
    }


}