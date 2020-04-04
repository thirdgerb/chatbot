<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Pipeline;

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Prototype\Session\ASessionPipe;
use Commune\Message\Blueprint\QuestionMsg;
use Commune\Shell\Blueprint\Question\Answerable;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class QuestionPipe extends ASessionPipe
{
    /**
     * @param ShlSession $session
     * @return ShlSession
     */
    protected function after($session)
    {
        $outputs = $session->getShellOutputs();
        $question = null;
        foreach ($outputs as $output) {
            $message = $output->message;
            if ($message instanceof QuestionMsg) {
                $question = $message;
            }
        }

        if (isset($question)) {
            $session->storage->setQuestion($question);
        }

        return $session;
    }

    /**
     * @param ShlSession $session
     * @return ShlSession
     */
    protected function before($session)
    {
        $question = $session->storage->getQuestion();
        if (empty($question)) {
            return $session;
        }

        $ghostInput = $session->ghostInput;

        // 让 question 自己判断答案.
        if ($question instanceof Answerable) {
            return $question->parse($session);
        }

        return $this->isNullable($session, $question, $ghostInput)
            ?? $this->isInSuggestions($session, $question, $ghostInput)
            // 没有匹配到任何答案
            ?? $session;
    }

    protected function isNullable(
        ShlSession $session,
        QuestionMsg $question,
        GhostInput $ghostInput
    ) : ? ShlSession
    {
        if (
            $ghostInput->shm->message->isEmpty()
            && $question->isNullable()
        ) {
            $defaultAnswers = $question->getDefaultAnswers();
            $ghostInput
                ->cph
                ->choice
                ->setChoices($defaultAnswers);
            return $session;
        }

        return null;
    }

    protected function isInSuggestions(
        ShlSession $session,
        QuestionMsg $question,
        GhostInput $ghostInput
    ) : ? ShlSession
    {
        $text = $ghostInput->getTrimmedText();
        if (!isset($text)) {
            return null;
        }

        // 对建议进行匹配, 是唯一的.
        if ($question->getMaxChoiceCount() !== 1) {
            return null;
        }

        // 没有选项匹配个啥劲呢
        $suggestions = $question->getSuggestions();
        if (empty($suggestions)) {
            return null;
        }

        $answers = $question->getAnswers();
        $matchedIndex = null;

        // 避免大小写之类的问题
        $text = StringUtils::normalizeString($text);
        $matched = 0;

        foreach ($suggestions as $index => $suggestion) {
            $indexStr = StringUtils::normalizeString(strval($index));

            if ($matched > 1) {
                break;
            }

            // 完全匹配的情况.
            if ($indexStr === $text) {
                $matchedIndex = $index;
                $matched ++;
                continue;
            }

            // 对索引进行部分匹配.
            if (strstr($indexStr, $text) !== false) {
                $matchedIndex = $index;
                $matched ++;
                continue;
            }

            // 对内容进行部分匹配
            $suggestion = StringUtils::normalizeString($suggestion);
            // 如果是其中一部分.
            if (strstr($suggestion, $text) !== false) {
                $matchedIndex = $index;
                $matched ++;
                continue;
            }
        }

        if ($matched !== 1) {
            return null;
        }

        $ghostInput
            ->cph
            ->choice
            ->addChoice($matchedIndex, $answers[$matched]);

        return $session;
    }

}