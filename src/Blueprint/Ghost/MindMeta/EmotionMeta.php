<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindMeta;

use Commune\Blueprint\Ghost\MindDef\AliasesForEmotion;
use Commune\Ghost\IMindDef\IEmotionDef;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Wrapper;


/**
 * 情感模块的元数据. 用于对多种匹配规则进行合并.
 * 不适合用于遍历所有情感进行主动匹配, 适合用于将已有的匹配信息被动归类
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name                  情感的id.
 * @property-read string $title                 情感的标题
 * @property-read string $desc                  情感的简介
 * @property-read string[] $opposites           对立的情绪
 * @property-read string[] $matchers            自定义的匹配逻辑.
 */
class EmotionMeta extends AbsOption implements DefMeta
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'opposites' => [],
            'matchers' => [],
        ];
    }

    public function __get_matcher() : array
    {
        return array_map(
            function (string $matcher) {
                return AliasesForEmotion::getOriginFromAlias($matcher);
            },
            $this->_data['matchers'] ?? []
        );
    }

    public function __set_matcher(string $name, array $matchers) : void
    {
        $this->_data[$name] = array_map(
            function(string $matcher) {
                return AliasesForEmotion::getAliasOfOrigin($matcher);
            },
            $matchers
        );
    }

    public static function relations(): array
    {
        return [];
    }

    public function toWrapper(): Wrapper
    {
        return new IEmotionDef($this);
    }


}