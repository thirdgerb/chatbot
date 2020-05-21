<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Tools;

use Commune\Blueprint\Ghost\Callables\Prediction;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\MindReg\EmotionReg;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Framework\Command\ICommandDef;
use Commune\Protocals\HostMsg\Convo\EventMsg;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Protocal\Protocal;
use Commune\Support\SoundLike\SoundLikeInterface;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMatcher implements Matcher
{
    /**
     * @var InputMsg
     */
    protected $input;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var array
     */
    protected $injectionContext;

    /**
     * @var array
     */
    protected $matchedParams = [];

    /**
     * @var bool
     */
    protected $matched = false;


    /**
     * @param $caller
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    protected function call($caller)
    {
        if (
            is_string($caller)
            && class_exists($caller)
            && method_exists($caller, '__invoke')
        ) {
            $caller = [$caller, '__invoke'];
        }

        return $this->cloner->container->call($caller, $this->injectionContext);
    }

    public function getMatchedParams(): array
    {
        return $this->matchedParams;
    }

    public function isMatched(): bool
    {
        return $this->matched;
    }

    public function refresh(): Matcher
    {
        $this->matched = false;
        $this->matchedParams = [];
        return $this;
    }

    public function isEvent(string $eventName): Matcher
    {
        $message = $this->input->getMessage();
        if (
            $message instanceof EventMsg
            && $message->getEventName() === $eventName
        ) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $eventName;
        }

        return $this;
    }

    public function isEventIn(array $eventNames): Matcher
    {
        $message = $this->input->getMessage();
        if (!$message instanceof EventMsg) {
            return $this;
        }
        $actual = $message->getEventName();

        foreach ($eventNames as $eventName) {
            if ($eventName == $actual) {
                $this->matched = true;
                $this->matchedParams[__FUNCTION__] = $eventName;
                return $this;
            }
        }

        return $this;
    }

    /**
     * @param callable|Prediction|string $prediction
     * @return Matcher
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    public function expect($prediction): Matcher
    {
        $bool = $this->call($prediction);
        if ($bool) {
            $this->matched = true;
        }

        return $this;
    }

    public function is(string $text): Matcher
    {
        $expect = StringUtils::normalizeString($text);
        $actual = $this->input->getNormalizedText();
        if ($expect === $actual) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $text;
        }

        return $this;
    }

    public function isEmpty() : Matcher
    {
        if ($this->input->getMessage()->isEmpty()) {
            $this->matched = true;
        }

        return $this;
    }

    public function pregMatch(string $pattern): Matcher
    {
        $text = $this->input->getMsgText();
        $matches = [];
        $matched = preg_match($pattern, $text, $matches);
        if ($matched) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $matches;
        }

        return $this;
    }

    public function isVerbal() : Matcher
    {
        $message = $this->input->getMessage();
        if ($message instanceof VerbalMsg) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $message;
        }

        return $this;
    }

    public function isInstanceOf(string $messageClazz): Matcher
    {
        $message = $this->input->getMessage();
        if (is_a($message, $messageClazz, TRUE)) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $message;
        }

        return $this;
    }

    public function isProtocal(string $protocalName): Matcher
    {
        $message = $this->input->getMessage();
        if (
            $message instanceof Protocal
            && is_a($message, $protocalName, TRUE)
        ) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $message;
        }

        return $this;
    }

    public function soundLike(
        string $text,
        string $lang = SoundLikeInterface::ZH
    ): Matcher
    {
        return $this->soundLikePart(
            $text,
            $lang,
            SoundLikeInterface::COMPARE_EXACTLY
        );
    }

    public function soundLikePart(
        string $text,
        string $lang = SoundLikeInterface::ZH,
        int $type = SoundLikeInterface::COMPARE_ANY_PART
    ): Matcher
    {
        $message = $this->input->getMessage();
        /**
         * @var SoundLikeInterface $soundLike
         */
        $soundLike = $this
            ->cloner
            ->container
            ->make(SoundLikeInterface::class);

        if (!$message instanceof VerbalMsg) {
            $this->matched = true;
        }

        $matched = $soundLike->soundLike(
            $message->getText(),
            $text,
            $lang,
            $type
        );

        if ($matched) {
            $this->matched = true;
        }

        return $this;
    }


    public function matchEntity(string $entityName): Matcher
    {
        if (!$this->input->isMsgType(VerbalMsg::class)) {
            return $this;
        }

        $mind = $this->cloner->mind;
        $entityReg = $mind->entityReg();

        if (!$entityReg->hasDef($entityName)) {
            return $this;
        }

        $def = $entityReg->getDef($entityName);

        $text = $this->input->getNormalizedText();

        $synonymReg = $mind->synonymReg();
        $entities = $def->match($text, $synonymReg);

        if (!empty($entities)) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $entities;
        }

        return $this;
    }


    public function isAnswer(string $answer) : Matcher
    {
        $actual = $this->input->comprehension->answer->getAnswer();
        if ($actual === $answer) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $answer;
        }

        return $this;
    }

    public function isAnyAnswer(): Matcher
    {
        $actual = $this->input->comprehension->answer->getAnswer();
        if (isset($actual)) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $actual;
        }

        return $this;
    }

    public function isChoice($suggestionIndex): Matcher
    {
        $choice = $this->input->comprehension->choice->getChoice();
        if (isset($choice) && $choice == $suggestionIndex) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $suggestionIndex;
        }

        return $this;
    }

    public function hasChoiceIn(array $choices) : Matcher
    {
        $actual = $this->input->comprehension->choice->getChoice();
        foreach ($choices as $choice) {
            if ($actual == $choice) {
                $this->matchedParams[__FUNCTION__] = $choice;
                return $this;
            }
            return $choice;
        }

        return $this;
    }

    public function isCommand(string $signature) : Matcher
    {
        $cmd = $this->input
            ->comprehension
            ->command;
        $cmdName = $cmd->getCmdName();

        if (!isset($cmdStr)) {
            return $this;
        }

        $def = ICommandDef::makeBySignature($signature);
        if ($cmdName === $def->getCommandName()) {
            $cmdStr = $cmd->getCmdStr();
            $cmdMessage = $def->parseCommandMessage($cmdStr);

            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $cmdMessage;
        }

        return $this;
    }

    public function hasKeywords(array $keyWords, array $blacklist = []): Matcher
    {
        if (empty($keyWords)) {
            return $this;
        }

        if (!$this->input->isMsgType(VerbalMsg::class)) {
            return $this;
        }

        // 先尝试用分词来做
        $tokenize = $this->input->comprehension->tokens;
        if ($tokenize->hasTokens()) {
            $tokens = $tokenize->getTokens();
            if (empty($tokens)) {
                return $this;
            }

            if (
                ArrayUtils::expectTokens($tokens, $keyWords, true)
                && !ArrayUtils::expectTokens($tokens, $blacklist, false)
            ) {
                $this->matched = true;
                return $this;
            }
        }

        // 然后用字符串来做

    }

    public function feels(string $emotionName) : Matcher
    {
        $reg = $this->cloner->mind->emotionReg();

        if (!$reg->hasDef($emotionName)) {
            return $this;
        }

        $emotion = $this
            ->input
            ->comprehension
            ->emotion;

        $has = $emotion->hasEmotion($emotionName);

        if (!isset($has)) {
            $def = $reg->getDef($emotionName);
            $has = $def->feels($this->cloner, $this->injectionContext);
            $emotion->setEmotion($emotionName, $has);
        }

        if ($has) {
            $this->matched = true;
            $this->matchedParams[__FUNCTION__] = $emotionName;
        }

        return $this;
    }

    public function isPositive(): Matcher
    {
        return $this->feels(EmotionReg::EMO_POSITIVE);
    }

    public function isNegative(): Matcher
    {
        return $this->feels(EmotionReg::EMO_NEGATIVE);
    }

    public function needHelp(): Matcher
    {
        // TODO: Implement needHelp() method.
    }

    public function isIntent(string $intentName): Matcher
    {
        // TODO: Implement isIntent() method.
    }

    public function isIntentIn(array $intentNames): ? string
    {
        // TODO: Implement isIntentIn() method.
    }

    public function isAnyIntent(): ? string
    {
        // TODO: Implement isAnyIntent() method.
    }

    public function hasPossibleIntent(string $intentName): bool
    {
        // TODO: Implement hasPossibleIntent() method.
    }

    public function hasEntity(string $entityName): bool
    {
        // TODO: Implement hasEntity() method.
    }

    public function hasEntityValue(string $entityName, $expect): bool
    {
        // TODO: Implement hasEntityValue() method.
    }


}