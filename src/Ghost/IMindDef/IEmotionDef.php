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
     */
    public function feels(
        Cloner $cloner,
        array $injectionContext = []
    ): bool
    {
        $comprehension = $cloner->input->comprehension;
        $name = $this->getName();


                // 是否已经有匹配结果了
        $feel = $this->hasEmotionComprehended($comprehension, $name)
                // 是否有相反的情绪
            ?? $this->hasOppositeEmotions($comprehension, $name)
                // 是否命中的意图是该情绪的子集
            ?? $this->hasMatchedIntentEmotion($comprehension, $cloner, $name)
                // 运行自定义的情绪校验器.
            ?? $this->runEmotionMatchers($comprehension, $name)
                // 没匹配到就是没有.
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