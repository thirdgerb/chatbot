<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Codable;

use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Ghost\IMindDef\IIntentDef;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AnnotationReflector
{
    /**
     * @var string
     */
    public $title = '';

    /**
     * @var string
     */
    public $desc = '';

    /**
     * @var string
     */
    public $intent = null;

    /**
     * @var string
     */
    public $spell = '';

    /**
     * @var string[]
     */
    public $examples = [];

    /**
     * @var string[]
     */
    public $entities = [];

    /**
     * @var string[]
     */
    public $regex = [];

    /**
     * @var string
     */
    public $signature = '';

    public function asIntentMeta(string $name) : IntentMeta
    {
        $def = new IIntentDef([
            // 意图的名称
            'name' => $name,
            // 意图的标题, 应允许用标题来匹配.
            'title' => $this->title,
            // 意图的简介. 可以作为选项的内容.
            'desc' => $this->desc,
            // 意图的别名. 允许别名中的意图作为精确匹配规则.
            'alias' => $this->intent,
            // 精确命中
            'spell' => $this->spell,
            // 例句, 用 []() 标记, 例如 "我想知道[北京](city)[明天](date)天气怎么样"
            'examples' => $this->examples,
            // 作为命令.
            'signature' => $this->signature,
            // entityNames
            'entityNames' => $this->entities,
            // 关键字
            'keywords' => [],
            // 正则
            'regex' => $this->regex,
            // 命中任意 entity
            'ifEntity' => [],
            // 自定义校验器. 字符串, 通常是类名或者方法名.
            'matcher' => null,
        ]);

        return $def->toMeta();
    }

    public static function create(string $docComment) : self
    {
        $ins = new static();

        $ins->title = StringUtils::fetchAnnotation($docComment, 'title')[0] ?? '';
        $ins->desc = StringUtils::fetchAnnotation($docComment, 'desc')[0] ?? '';
        $ins->intent = StringUtils::fetchAnnotation($docComment, 'intent')[0] ?? null;
        $ins->signature = StringUtils::fetchAnnotation($docComment, 'signature')[0] ?? '';

        $ins->examples = StringUtils::fetchAnnotation($docComment, 'example');
        $ins->regex = StringUtils::fetchAnnotation($docComment, 'regex');

        $ins->spell = StringUtils::fetchAnnotation($docComment, 'spell')[0] ?? '';
        $ins->entities = explode(
            ',',
            StringUtils::fetchAnnotation($docComment, 'entities')[0] ?? ''
        );


        return $ins;
    }
}