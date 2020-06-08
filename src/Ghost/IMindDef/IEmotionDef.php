<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindDef;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\MindMeta\EmotionMeta;
use Commune\Blueprint\Ghost\MindDef\EmotionDef;
use Commune\Protocals\Comprehension;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IEmotionDef implements EmotionDef
{

    /**
     * @var EmotionMeta
     */
    protected $meta;

    /**
     * IEmotionDef constructor.
     * @param EmotionMeta $meta
     */
    public function __construct(EmotionMeta $meta)
    {
        $this->meta = $meta;
    }


    public function getName(): string
    {
        return $this->meta->name;
    }

    public function getTitle(): string
    {
        return $this->meta->title;
    }

    public function getDescription(): string
    {
        return $this->meta->desc;
    }

    /**
     * @param Cloner $cloner
     * @param array $injectionContext
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    public function feels(
        Cloner $cloner,
        array $injectionContext = []
    ): bool
    {
        $comprehension = $cloner->input->comprehension;
        $name = $this->getName();

        $feel = $this->hasEmotionComprehended($comprehension, $name)
            ?? $this->hasOppositeEmotions($comprehension, $name)
            ?? $this->hasMatchedIntentEmotion($comprehension, $cloner, $name)
            ?? $this->hasDefinedEmotionIntents($comprehension, $name)
            ?? $this->runEmotionMatchers($comprehension, $name)
            ?? false;
        return $feel;
    }

    protected function hasOppositeEmotions(Comprehension $comprehension, string $name) : ? bool
    {
        $opposites = $this->meta->opposites;
        if (empty($opposites)) {
            return null;
        }

        $emotionModule = $comprehension->emotion;
        foreach ($opposites as $opposite) {
            $has = $emotionModule->isEmotion($opposite);
            if (isset($has)) {
                return $this->setEmotion($comprehension, $name, !$has);
            }
        }

        return null;
    }

    protected function runEmotionMatchers(Comprehension $comprehension, string $name) : ? bool
    {
        $matchers = $this->meta->matchers;
        if (empty($matchers)) {
            return $this->setEmotion($comprehension, $name, false);
        }
        return null;
    }

    protected function hasDefinedEmotionIntents(Comprehension $comprehension, string $name) : ? bool
    {
        $intents = $this->meta->emotionalIntents;
        $intention = $comprehension->intention;
        // 检查是否有相关的 intents 命中了.
        if (!empty($intents)) {
            $matched = $intention->matchAnyIntent($intents);
            if (isset($matched)) {
                return $this->setEmotion($comprehension, $name, true);
            }
        }
        return null;
    }

    protected function hasEmotionComprehended(Comprehension $comprehension, string $name) : ? bool
    {
        return $comprehension->emotion->isEmotion($name);
    }

    protected function hasMatchedIntentEmotion(
        Comprehension $comprehension,
        Cloner $cloner,
        string $name
    ) : ? bool
    {
        $matched = $comprehension->intention->getMatchedIntent();
        if (!isset($matched)) {
            return null;
        }

        $reg = $cloner->mind->intentReg();
        if (!$reg->hasDef($matched)) {
            return null;
        }

        $def = $reg->getDef($matched);
        $extends = $def->getEmotions();

        return in_array($name, $extends)
            ? $this->setEmotion($comprehension, $name, true)
            : null;
    }

    protected function setEmotion(Comprehension $comprehension, string $name, bool $bool) : bool
    {
        $emotionModule = $comprehension->emotion;
        $emotionModule->setEmotion($name, $bool);
        $opposites = $this->meta->opposites;

        if (!empty($opposites)) {
            foreach ($opposites as $emotionName) {
                $emotionModule->setEmotion($emotionName, !$bool);
            }
        }
        return $bool;
    }


    public function toMeta(): Meta
    {
        return $this->meta;
    }

    /**
     * @param EmotionMeta $meta
     * @return Wrapper
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        return new static($meta);
    }


}