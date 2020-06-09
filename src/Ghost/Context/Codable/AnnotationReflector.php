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
 * 从注解中获取 intent(意图) 的基本定义.
 *
 * 合法的注解有:
 *
 * @title 标题, 唯一
 * @desc 简介, 唯一
 * @intent 意图的别名, 唯一
 * @spell 可以精确命中意图的字符串. 唯一
 * @example 意图的语料, 用 [实体名](实体值) 来标注实体
 * @entity 用来定义意图的实体参数, 数组类型的参数要加 "[]"作为后缀. 例如 "city", 或者 "date[]"
 * @signature 用来定义命令. 唯一
 *
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
    public $extends = [];

    /**
     * @var string[]
     */
    public $regex = [];

    /**
     * @var string
     */
    public $signature = '';

    public function asIntentMeta(string $name, string $matcher = null) : IntentMeta
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
            // 如果命中其它意图, 也会命中当前意图.
            'extends' => $this->extends,
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
            'matcher' => $matcher,
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

        $examples = StringUtils::fetchAnnotation($docComment, 'example') ?? [];
        $ins->examples = array_filter($examples, [StringUtils::class, 'isNotEmptyStr']) ?? [];

        $regex = StringUtils::fetchAnnotation($docComment, 'regex')  ?? [];
        $ins->regex = array_filter($regex, [StringUtils::class, 'isNotEmptyStr']);

        $ins->spell = StringUtils::fetchAnnotation($docComment, 'spell')[0] ?? '';

        $entities = StringUtils::fetchAnnotation($docComment, 'entity') ?? [];
        $ins->entities = array_filter($entities, [StringUtils::class, 'isNotEmptyStr']);

        $ins->extends = StringUtils::fetchAnnotation($docComment, 'extend') ?? [];

        return $ins;
    }

}