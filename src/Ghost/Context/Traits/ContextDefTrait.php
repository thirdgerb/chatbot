<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Traits;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\ContextStrategyOption;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\IContext;
use Commune\Ghost\IMindDef\IIntentDef;
use Commune\Ghost\IMindDef\IMemoryDef;
use Commune\Ghost\Stage\InitStage;
use Commune\Support\Option\AbsOption;
use Commune\Blueprint\Ghost\MindDef\AliasesForAuth;
use Commune\Blueprint\Ghost\MindDef\AliasesForContext;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


/**
 *
 * 这个 Trait 尽量把纯定义的参数拆分出来
 * 方便通过继承, 快速实现新的基于配置的 context def
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $contextWrapper
 *
 * @property-read int $priority
 * @property-read string[] $queryNames
 *
 * @property-read IntentMeta|null $asIntent
 * @property-read string[] $memoryScopes
 * @property-read array $memoryAttrs
 *
 * @property-read ContextStrategyOption $strategy
 *
 *
 *
 * @mixin AbsOption
 * @mixin ContextDef
 */
trait ContextDefTrait
{

    /**
     * @var MemoryDef
     */
    protected $_asMemoryDef;

    /**
     * @var StageDef
     */
    protected $_asStageDef;


    public static function validate(array $data): ? string /* errorMsg */
    {
        $name = $data['name'] ?? '';

        if (!ContextUtils::isValidContextName($name)) {
            return "contextName $name is invalid";
        }

        return parent::validate($data);
    }

    public function __get_contextWrapper() : string
    {
        $wrapper = $this->_data['contextWrapper'] ?? '';
        $wrapper = empty($wrapper)
            ? IContext::class
            : $wrapper;

        return AliasesForContext::getOriginFromAlias($wrapper);
    }

    public function __set_contextWrapper($name, $value) : void
    {
        $this->_data[$name] = AliasesForContext::getAliasOfOrigin($value);
    }


    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getQueryNames(): array
    {
        return $this->queryNames;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->desc;
    }

    /*------ wrap -------*/

    /**
     * @return ContextMeta
     */
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
            'wrapper' => static::class,
            'config' => $config
        ]);
    }


    /**
     * @param Meta $meta
     * @return Wrapper
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        if (!$meta instanceof ContextMeta) {
            throw new InvalidArgumentException(
                __METHOD__
                . ' only accept meta of subclass ' . ContextMeta::class
            );
        }

        $config = $meta->config;
        $config['name'] = $meta->name;
        $config['title'] = $meta->title;
        $config['desc'] = $meta->desc;
        return static::create($config);
    }

    /*------ memory ------*/

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

    public function getScopes(): array
    {
        return $this->memoryScopes;
    }

    /*------ init stage ------*/

    public function asStageDef() : StageDef
    {
        if (isset($this->_asStageDef)) {
            return $this->_asStageDef;
        }

        $asIntent  = $this->getAsIntent();
        return $this->_asStageDef = new InitStage([
            'name' => $this->name,
            'contextName' => $this->name,
            'title' => $this->title,
            'desc' => $this->desc,
            'stageName' => '',
            'asIntent' => $asIntent,
        ]);
    }

    protected function getAsIntent() : IntentMeta
    {
        $asIntent = $this->asIntent;
        if (!isset($asIntent)) {
            $intentDef = new IIntentDef([
                'name' => $this->name,
                'title' => $this->title,
                'desc' => $this->desc,
                'examples' => [],
            ]);

            $asIntent = $intentDef->toMeta();
        }
        return $asIntent;
    }

    public function getPredefinedStageNames(bool $isFullname = false): array
    {
        $names = [];
        foreach ($this->eachPredefinedStage() as $stage) {
            /**
             * @var StageDef $stage
             */
            $names[] = $isFullname
                ? $stage->getName()
                : $stage->getStageShortName();
        }
        return $names;
    }


    public function getStrategy(Dialog $current): ContextStrategyOption
    {
        return $this->strategy;
    }

    /*------ to context -------*/

    public function wrapContext(Cloner $cloner, Ucl $ucl): Context
    {
        return call_user_func(
            [$this->contextWrapper, Context::CREATE_FUNC],
            $cloner,
            $ucl
        );
    }
}