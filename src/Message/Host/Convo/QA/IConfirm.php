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

use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocals\Comprehension;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\MindDef\EmotionDef;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocals\HostMsg\Convo\QA\Confirm;
use Commune\Protocals\HostMsg\Convo\QA\Confirmation;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IConfirm extends IQuestionMsg implements Confirm
{
    const POSITIVE_INDEX = 'y';
    const NEGATIVE_INDEX = 'n';

    protected $acceptAnyTextAsValue = false;

    public static function newConfirm(
        string $query,
        bool $default = null,
        string $positive = null,
        string $negative = null,
        array $routes = []
    )
    {
        $suggestions = [];

        $suggestions[self::POSITIVE_INDEX] = $positive ?? self::POSITIVE_LANG;
        $suggestions[self::NEGATIVE_INDEX] = $negative ?? self::NEGATIVE_LANG;

        $default = isset($default)
            ? (
                $default ? self::POSITIVE_INDEX : self::NEGATIVE_INDEX
            )
            : null;

        $data = [
            'query' => $query,
            'suggestions' => $suggestions,
            'routes' => $routes,
            'default' => $default,

        ];

        return new static($data);
    }

    protected function parseAnswerByMatcher(Cloner $cloner): ? AnswerMsg
    {
        $matcher = $cloner->matcher;

        if ($matcher->refresh()->isPositive()->truly()) {
            return $this->newAnswer($this->_data['suggestions'][self::POSITIVE_INDEX], self::POSITIVE_INDEX);
        }

        if($matcher->refresh()->isNegative()->truly()) {
            return $this->newAnswer($this->_data['suggestions'][self::NEGATIVE_INDEX], self::NEGATIVE_INDEX);
        }

        return null;
    }

    protected function parseInputText(string $text): string
    {
        $text = parent::parseInputText($text);
        // 处理一些常见的单字符表示.
        switch($text) {
            case 'y' :
            case '是' :
            case '好' :
            case '对' :
            case '1' :
                return self::POSITIVE_INDEX;
            case 'n' :
            case '否' :
            case '不' :
            case '别' :
            case '0' :
                return self::NEGATIVE_INDEX;
            default:
                return $text;
        }
    }

    /**
     * @param Confirmation $answer
     * @param Comprehension $comprehension
     * @return AnswerMsg
     */
    protected function setAnswerToComprehension(AnswerMsg $answer, Comprehension $comprehension): AnswerMsg
    {
        $positive = $answer->isPositive();

        $comprehension->emotion->setEmotion(EmotionDef::EMO_POSITIVE, $positive);
        $comprehension->emotion->setEmotion(EmotionDef::EMO_NEGATIVE, !$positive);

        return $answer;
    }

    protected function makeAnswerInstance(
        string $answer,
        $choice = null,
        string $route = null
    ): AnswerMsg
    {
        return new IConfirmation([
            'answer' => $answer,
            'choice' => $choice,
            'routes' => $route,
        ]);
    }


    public function setPositive(string $suggestion, Ucl $ucl = null) : Confirm
    {
        $this->addSuggestion(
            $suggestion,
            self::POSITIVE_INDEX,
            $ucl ? $ucl->encode() : null
        );
        return $this;
    }

    public function setNegative(string $suggestion, Ucl $ucl = null) : Confirm
    {
        $this->addSuggestion(
            $suggestion,
            self::NEGATIVE_INDEX,
            $ucl ? $ucl->encode() : null
        );
        return $this;
    }

    public function getPositiveSuggestion(): string
    {
        return $this->_data['suggestions'][self::POSITIVE_INDEX] ?? 'y';
    }

    public function getNegativeSuggestion(): string
    {
        return $this->_data['suggestions'][self::NEGATIVE_INDEX] ?? 'n';
    }


    public function getText(): string
    {
        $query = $this->query;
        $p = $this->getPositiveSuggestion();
        $n = $this->getNegativeSuggestion();

        return "$query ($p|$n)";
    }
}