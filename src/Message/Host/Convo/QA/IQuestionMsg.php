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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Contracts\Trans\Translator;
use Commune\Ghost\Support\ContextUtils;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property string $query
 * @property string[] $suggestions
 * @property string[] $routes
 * @property string|null $default
 * @property int $mode
 *
 *
 * @property-read bool $translated
 */
class IQuestionMsg extends AbsMessage implements QuestionMsg
{

    const MODE = self::MATCH_INDEX
        | self::MATCH_SUGGESTION
        | self::MATCH_INTENT
        | self::MATCH_ANY;

    protected $slots = [];

    /**
     * @param string $query
     * @param null $default
     * @param array $suggestions
     * @param array $routes
     * @return static
     */
    public static function instance(
        string $query,
        $default = null,
        array $suggestions = [],
        array $routes = []
    ) : IQuestionMsg
    {
        $question = new static([
            'query' => $query,
            'default' => $default,
            'routes' => array_map('strval', $routes)
        ]);

        foreach ($suggestions as $index => $suggestion) {
            $question->addSuggestion($suggestion, $index);
        }

        return $question;
    }

    public static function stub(): array
    {
        return [
            'query' => '',
            'suggestions' => [],
            'routes' => [],
            'default' => null,
            'mode' => self::MODE,

            'translated' => false,
        ];
    }


    public static function create(array $data = []): Struct
    {
        return new static($data);
    }

    /*-------- match mode --------*/
    public function isMatchMode(int $mode): bool
    {
        return ($mode & $this->mode) > 0;
    }

    public function setMatchMode(int $mode): void
    {
        $this->mode = $mode;
    }

    public function withoutMatchMode(int $mode): void
    {
        $this->mode = $this->mode ^ $mode;
    }

    public function getMatchMode(): int
    {
        return $this->mode;
    }


    /*-------- parser --------*/

    public function parse(Cloner $cloner): ? AnswerMsg
    {

        $input = $cloner->input;
        $comprehension = $cloner->comprehension;
        $answer = $comprehension->answer->getAnswer();

        if (isset($answer)) {
            return $answer;
        }


        $answer = $this->parseAnswerByType($input)
            ?? $this->parseAnswerByVerbal($input);

        return isset($answer)
            ? $this->setAnswerToComprehension($answer, $comprehension)
            : null;
    }

    public function match(Cloner $cloner): ? AnswerMsg
    {
        return $this->parseAnswerByMatcher($cloner);
    }


    protected function parseAnswerByType(InputMsg $inputMsg) : ? AnswerMsg
    {
        return null;
    }

    /**
     * 开放给外部可以用于单测的方法.
     * @param InputMsg $input
     * @return AnswerMsg|null
     */
    public function parseAnswerByVerbal(InputMsg $input) : ? AnswerMsg
    {
        $message = $input->getMessage();
        if (!$message instanceof VerbalMsg) {
            return null;
        }

        return $this->isDefault($message)
            ?? $this->isInSuggestions($message)
            ?? (
                $this->isMatchMode(self::MATCH_ANY)
                ? $this->acceptAnyVerbalAnswer($message)
                : null
            )
            ?? null;
    }



    protected function setAnswerToComprehension(AnswerMsg $answer, Comprehension $comprehension) : AnswerMsg
    {
        $comprehension->answer->setAnswer($answer);

        $choice = $answer->getChoice();
        $routes = $this->getRoutes();

        if (isset($routes[$choice])) {
            $ucl = $routes[$choice];
            $comprehension->intention->setMatchedIntent($ucl->getStageFullname());
        }

        return $answer;
    }

    protected function acceptAnyVerbalAnswer(VerbalMsg $message) : ? AnswerMsg
    {
        $text = $message->getText();
        return $this->newAnswer($text);
    }

    protected function parseInputText(string $text) : string
    {
        $text = StringUtils::normalizeString($text);
        return $text;
    }

    protected function isInSuggestions(VerbalMsg $message) : ? AnswerMsg
    {
        $matchedSuggestions = [];

        $text = $this->parseInputText($message->getText());

        if (StringUtils::isEmptyStr($text)) {
            return null;
        }

        $matchIndex = $this->isMatchMode(self::MATCH_INDEX);
        $matchSuggestion = $this->isMatchMode(self::MATCH_SUGGESTION);

        if (!$matchIndex && !$matchSuggestion) {
            return null;
        }

        $suggestions = $this->suggestions;
        if (empty($suggestions)) {
            return null;
        }

        foreach ($suggestions as $index => $suggestion) {

            if ($matchIndex) {
                $indexStr = StringUtils::normalizeString(strval($index));
                // index 需要完全匹配的情况.
                if ($indexStr === $text) {
                    return $this->newAnswer($suggestion, $index);
                }
            }

            // 对内容进行部分匹配
            if ($matchSuggestion) {
                $suggestion = StringUtils::normalizeString($suggestion);
                if ($suggestion === $text) {
                    return $this->newAnswer($suggestion, $index);
                }
                // 如果是其中一部分.
                if (mb_strstr($suggestion, $text) !== false) {
                    $matchedSuggestions[] = $index;
                }
            }
        }

        if (count($matchedSuggestions) === 1) {
            $index = $matchedSuggestions[0];
            return $this->newAnswer($this->suggestions[$index], $index);
        }

        return null;

    }

    protected function isDefault(VerbalMsg $message) : ? AnswerMsg
    {
        $default = $this->default;
        if (isset($default) && $message->isEmpty()) {
            return $this->newAnswer(
                $this->_data['suggestions'][$default] ?? '',
                $default
            );
        }

        return null;
    }

    /**
     * 考虑这个方法意义不大, 暂时保留.
     * @param Cloner $cloner
     * @return AnswerMsg|null
     */
    public function parseByMatchedIntents(Cloner $cloner) : ? AnswerMsg
    {
        if (empty($this->routes)) {
            return null;
        }

        $names = $cloner->comprehension
            ->intention
            ->getPossibleIntentNames(true);

        if (empty($names)) {
            return null;
        }

        $routes = $this->getRoutes();
        // 按名称匹配
        $map = [];
        // 按正则匹配, 通常只会有一个, 估计.
        $patterns = [];
        foreach ($routes as $index => $route) {
            $fullname = $route->getStageFullname();
            if (ContextUtils::isWildcardIntentPattern($fullname)) {
                $patterns[$fullname] = $index;
            } else {
                $map[$fullname] = $index;
            }
        }

        $hasPattern = count($patterns);
        foreach ($names as $name) {
            if (isset($map[$name])) {
                $index = $map[$name];
                return $this->newAnswer(
                    $this->suggestions[$index],
                    $index
                );

            } elseif (!$hasPattern) {
                continue;
            }

            foreach ($patterns as $index => $pattern) {
                $matched = ContextUtils::wildcardIntentMatch($pattern, $name);
                if (isset($matched)) {
                    return $this->newAnswer(
                        $this->suggestions[$index],
                        $index
                    );
                }
            }
        }

        return null;
    }


    protected function parseAnswerByMatcher(Cloner $cloner) : ? AnswerMsg
    {
        $matcher = $cloner->matcher->refresh();
        $ordinalInt = HostMsg\DefaultIntents::GUEST_DIALOG_ORDINAL;

        if ($matcher->isIntent($ordinalInt)->truly() === true) {
            $entities = $cloner
                ->comprehension
                ->intention
                ->getIntentEntities($ordinalInt);

            $index = strval($entities[HostMsg\DefaultIntents::GUEST_DIALOG_ORDINAL][0] ?? 0);

            $suggestions = $this->suggestions;
            if (isset($suggestions[$index])) {
                return $this->newAnswer($suggestions[$index], $index);
            }
        }

        return null;
    }

    /**
     * @return Ucl[]
     */
    public function getRoutes(): array
    {
        $routes = $this->routes;
        if (empty($routes)) {
            return [];
        }
        return array_map(
            function($route) {
                return Ucl::decode($route);
            },
            $routes
        );
    }


    protected function newAnswer(string $answer, $choice = null) : AnswerMsg
    {
        if (isset($choice)) {
            $route = $this->routes[$choice] ?? null;
        } else {
            $route = null;
        }
        return $this->makeAnswerInstance($answer, $choice, $route);
    }

    protected function makeAnswerInstance(
        string $answer,
        $choice = null,
        string $route = null
    ) : AnswerMsg
    {
        return new IAnswerMsg([
            'answer' => $answer,
            'choice' => $choice,
            'route' => $route
        ]);
    }

    /*-------- methods --------*/

    public function addDefault(string $choice): void
    {
        $this->_data['default'] = $choice;
    }


    public static function relations(): array
    {
        return [];
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    /**
     * @param string $suggestion
     * @param null $index
     * @param Ucl|null $ucl
     * @return mixed
     */
    public function addSuggestion(string $suggestion, $index = null, Ucl $ucl = null)
    {
        if (is_null($index)) {
            $this->_data['suggestions'][] = $suggestion;
            $keys = array_keys($this->_data['suggestions']);
            $index = end($keys);
        } else {
            $this->_data['suggestions'][$index] = $suggestion;
        }


        if (isset($ucl)) {
            $this->_data['routes'][$index] = $ucl->encode();
        }

        return $index;
    }

    public function isEmpty(): bool
    {
        return empty($this->_data['query']);
    }

    public function getProtocalId(): string
    {
        return $this->query;
    }

    public function getLevel(): string
    {
        return HostMsg::INFO;
    }

    public function getText(): string
    {
        $text = $this->query . "\n";

        foreach ($this->getSuggestions() as $index => $suggestion) {
            $text .= "[$index] $suggestion \n";
        }

        return $text;
    }

    public function translate(Translator $translator): void
    {
        if ($this->translated) {
            return;
        }

        $slots = $this->slots;

        // query
        $this->query = $translator->trans($this->query, $slots);

        // suggestions
        $this->suggestions = array_map(
            function($suggestion) use ($translator, $slots) {
                return $translator->trans($suggestion, $slots);
            },
            $this->suggestions
        );

        $this->translated = true;
    }

    public function withSlots(array $slots): QuestionMsg
    {
        $this->slots = $slots;
        return $this;
    }


}