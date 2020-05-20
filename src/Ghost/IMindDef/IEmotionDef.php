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
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindMeta\EmotionMeta;
use Commune\Blueprint\Ghost\MindDef\EmotionDef;
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

    public function feels(Dialog $dialog): bool
    {
        $intents = $this->meta->emotionalIntents;
        $cloner = $dialog->cloner;
        // 检查是否有相关的 intents 命中了.
        if (!empty($intents)) {
            $intention = $cloner->input->comprehension->intention;
            $matched = $intention->matchAnyIntent($intents);
            if (isset($matched)) {
                return true;
            }
        }

        $matchers = $this->meta->matchers;
        if (empty($matchers)) {
            return false;
        }

        $caller = $dialog->caller();
        foreach ($matchers as $matcherName) {
            // 校验所有的 matcher.
            if ($caller->predict($matcherName)) {
                return true;
            }
        }

        return false;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    /**
     * @param EmotionMeta $meta
     * @return Wrapper
     */
    public static function wrap(Meta $meta): Wrapper
    {
        return new static($meta);
    }


}