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

use Commune\Blueprint\Ghost\MindDef\AliasesForIntent;
use Commune\Ghost\IMindDef\IIntentDef;
use Commune\Support\Option\AbsMeta;
use Commune\Blueprint\Ghost\MindDef\IntentDef;

/**
 * 意图的元数据. 用于定义标准的意图.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $wrapper
 * @property-read string[] $examples
 * @property-read string[] $entityNames
 * @property-read string[] $emotions
 *
 * @property-read array $config
 *
 * @method IntentDef toWrapper(): Wrapper
 */
class IntentMeta extends AbsMeta implements DefMeta
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            // 意图的名称
            'name' => '',
            // wrapper
            'wrapper' => '',
            // 意图的标题, 应允许用标题来匹配.
            'title' => '',
            // 意图的简介. 可以作为选项的内容.
            'desc' => '',
            // 例句
            'examples' => [],

            // 实体参数的定义
            'entityNames' => [],

            // 意图代表情绪的定义
            'emotions' => [],

            // wrapper 的额外配置.
            'config' => [],
        ];
    }


    /**
     * @param array $data
     * @param string $name
     * @param string $title
     * @param string $desc
     * @param bool $force
     * @return array
     */
    public static function mergeStageInfo(
        array $data,
        string $name,
        string $title,
        string $desc,
        bool $force = false
    ) : array
    {
        if (empty($data['name']) || $force) {
            $data['name'] = $name;
        }

        if (empty($data['title']) || $force) {
            $data['title'] = $title;
        }

        if (empty($data['desc']) || $force) {
            $data['desc'] = $desc;
        }

        return $data;
    }

    public function __get_wrapper() : string
    {
        $wrapper = $this->_data['wrapper'] ?? '';
        $wrapper = empty($wrapper)
            ? IIntentDef::class
            : $wrapper;

        return AliasesForIntent::getOriginFromAlias( $wrapper);
    }

    public function __set_wrapper(string $name, $wrapper) : void
    {
        $this->_data[$name] = AliasesForIntent::getAliasOfOrigin(strval($wrapper));
    }

    public static function relations(): array
    {
        return [];
    }

}