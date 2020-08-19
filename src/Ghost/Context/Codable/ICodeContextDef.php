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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Context\CodeContext;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\ContextStrategyOption;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IMindDef\IMemoryDef;
use Commune\Ghost\Stage\InitStage;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name      当前配置的 ID
 * @property-read string $title     标题
 * @property-read string $desc      简介
 *
 *
 * ## 基础属性
 * @property-read int $priority                     语境的默认优先级
 *
 * @property-read string[] $auth                    用户权限
 * @property-read string[] $queryNames              context 请求参数键名的定义, 如果是列表则要加上 []
 *
 *
 * @property-read string[] $memoryScopes
 * @property-read array $memoryAttrs
 *
 * @property-read array $dependingNames
 *
 * @property-read ContextStrategyOption $strategy
 *
 */
class ICodeContextDef extends AbsOption implements  ContextDef
{
    /**
     * @var string
     */
    protected $_contextClass;

    /**
     * @var CodeDefCreator
     */
    protected $_creator;

    /**
     * @var Depending
     */
    protected $_depending;

    /**
     * @var AnnotationReflector
     */
    protected $_annotation;


    /**
     * @var MemoryDef
     */
    protected $_asMemoryDef;

    /**
     * @var StageDef
     */
    protected $_asStageDef;

    /**
     * @var StageMeta[]
     */
    protected $_stageMetaMap;


    public function __construct(string $contextClass, ContextMeta $meta = null)
    {
        $this->_contextClass = $contextClass;
        $this->_creator = $creator = new CodeDefCreator($contextClass);
        $this->_depending = $creator->getDependingBuilder();

        $contextName = isset($meta) ? $meta->name : $creator->getContextName();

        $option = $creator->getCodeContextOption();
        $config = isset($meta) ? $meta->config : [];

        $this->_annotation = $contextAnnotation = $creator->getContextAnnotation();

        parent::__construct([
            'name' => $contextName,
            'title' => isset($meta) ? $meta->title : $contextAnnotation->title,
            'desc' => isset($meta) ? $meta->desc : $contextAnnotation->desc,

            'priority' => $config['priority'] ?? $option->priority,

            'queryNames' => $config['queryNames'] ?? $option->queryNames,

            'memoryScopes' => $config['memoryScopes'] ?? $option->memoryScopes,

            'memoryAttrs' => $config['memoryAttrs']
                    ?? $this->_depending->attrs + $option->memoryAttrs,

            'dependingNames' => $config['dependingNames'] ?? array_keys($this->_depending->attrs),

            'strategy' => $config['strategy'] ?? $option->strategy,

        ]);
        unset($creator);
    }

    public static function relations(): array
    {
        return [
            'strategy' => ContextStrategyOption::class,
        ];
    }


    public static function stub(): array
    {
        return [
            // context 的全名. 同时也是意图名称.
            'name' => '',
            // context 的标题. 可以用于 精确意图校验.
            'title' => '',
            // context 的简介. 通常用于 askChoose 的选项.
            'desc' => '',
            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 0,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => [],

            // 定义 context 上下文记忆的作用域.
            // 相关作用域参数, 会自动添加到 query 参数中.
            // 作用域为空, 则是一个 session 级别的短程记忆.
            // 不为空, 则是长程记忆, 会持久化保存.
            'memoryScopes' => null,

            // memory 记忆体的默认值.
            'memoryAttrs' => [],

            // Context 启动时, 会依次检查的参数. 当这些参数都不是 null 时, 认为 Context::isPrepared
            'dependingNames' => [],

            'strategy' => [

            ],
        ];
    }

    public function asStageDef() : StageDef
    {
        return $this->_asStageDef
            ?? $this->_asStageDef = new InitStage([
                'name' => $this->name,
                'contextName' => $this->name,
                'title' => $this->title,
                'desc' => $this->desc,
                'stageName' => '',
                'asIntent' => $this->_annotation->asIntentMeta($this->name),
            ]);
    }

    public function asMemoryDef() : MemoryDef
    {
        return $this->_asMemoryDef
            ?? $this->_asMemoryDef = new IMemoryDef(new MemoryMeta([
                'name' => $this->name,
                'title' => $this->title,
                'desc' => $this->desc,
                'scopes' => $this->memoryScopes,
                'attrs' => $this->memoryAttrs,
            ]));
    }


    /**
     * @return StageMeta[]
     */
    public function getStageMetaMap(): array
    {
        if (isset($this->_stageMetaMap)) {
            return $this->_stageMetaMap;
        }

        $stages = $this->_creator->getMethodStageMetas();
        $dependingStages = $this->_depending->stages;
        $stages = $stages + $dependingStages;

        foreach ($stages as $stageMeta) {
            $shortName = $stageMeta->stageName;
            $this->_stageMetaMap[$shortName] = $stageMeta;
        }

        return $this->_stageMetaMap;
    }

    public function wrapContext(Cloner $cloner, Ucl $ucl): Context
    {
        return call_user_func(
            [$this->_contextClass, Context::CREATE_FUNC],
            $cloner,
            $ucl
        );
    }

    public function firstStage(): ? string
    {
        return CodeContext::FIRST_STAGE;
    }

    public function eachPredefinedStage(): \Generator
    {
        foreach ($this->getStageMetaMap() as $name => $stage) {
            yield $stage->toWrapper();
        }
    }

    public function getPredefinedStageNames(bool $isFullname = false): array
    {
        $map = $this->getStageMetaMap();
        return $isFullname
            ? array_map(function(StageMeta $meta) { return $meta->name;}, $map)
            : array_keys($map);
    }

    public function getPredefinedStage(string $name): ? StageDef
    {
        $meta = $this->getStageMetaMap()[$name] ?? null;
        return isset($meta)
            ? $meta->toWrapper()
            : null;
    }


    /*------ properties -------*/

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        $title = $this->title;
        return empty($title) ? $this->getName() : $title;
    }

    public function getDescription(): string
    {
        $desc = $this->desc;
        return empty($desc) ? $this->getName() : $desc;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getScopes(): array
    {
        return $this->memoryScopes;
    }

    public function getDependingNames(): array
    {
        return $this->dependingNames;
    }

    public function getQueryNames(): array
    {
        return $this->queryNames;
    }

    /**
     * @return null|string
     */
    public function getFirstStage(): ? string
    {
        return CodeContext::FIRST_STAGE;
    }

    public function getStrategy(Dialog $current): ContextStrategyOption
    {
        return $this->strategy;
    }


    /*-------- meta wrap --------*/

    public function toMeta(): Meta
    {
        $config = $this->toArray();
        unset($config['name']);
        unset($config['title']);
        unset($config['desc']);

        return new ContextMeta([
            'name' => $this->name,
            'title' => $this->title,
            'desc' => $this->desc,
            'wrapper' => $this->_contextClass,
            'config' => $config
        ]);
    }



    /**
     * @param Meta $meta
     * @return ContextDef
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        if (!$meta instanceof ContextMeta) {
            throw new InvalidArgumentException(
                __METHOD__
                . ' only accept meta of subclass ' . ContextMeta::class
            );
        }

        $className = $meta->wrapper;
        return new static($className, $meta);
    }

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return call_user_func([$this->_contextClass, CodeContext::FUNC_REDIRECT], $prev, $current);
    }
}