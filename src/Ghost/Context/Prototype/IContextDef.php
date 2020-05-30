<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Prototype;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\MindDef\ParamDef;
use Commune\Blueprint\Ghost\MindDef\ParamDefCollection;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Illuminate\Support\Collection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * ## 基础属性
 * @property-read int $priority         语境的默认优先级
 *
 * ## context wrapper
 * @property-read string|null $contextWrapper       Context 的包装器.
 *
 * ## 属性相关
 * @property-read ParamOption[] $queryParams
 *
 * ## Stage 相关
 * @property-read StageMeta $asStage                Stage 的定义.
 * @property-read MemoryMeta $asMemory              Memory 的定义
 * @property-read StageMeta[] $stages               Context 定义的 Stages
 *
 */
class IContextDef extends AbsOption implements ContextDef
{

    /**
     * @var ParamDefCollection
     */
    protected $_queries;

    /**
     * @var ParamDefCollection
     */
    protected $_entities;

    /**
     * @var ParamDefCollection
     */
    protected $_params;

    /**
     * @var Collection
     */
    protected $_stageMap;

    /**
     * @var MemoryDef
     */
    protected $_asMemory;

    /**
     * @var StageDef
     */
    protected $_asStage;

    /**
     * @var string
     */
    protected $_name;

    /**
     * @var string
     */
    protected $_desc;

    /**
     * @var string
     */
    protected $_title;

    public function __construct(
        string $name,
        string $title,
        string $desc,
        array $data
    )
    {
        $this->_name = $name;
        $this->_title = $title;
        $this->_desc = $desc;
        parent::__construct($data);
    }

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'priority' => 0,
            'contextWrapper' => null,
            'queryParams' => [],
            'asStage' => [],
            'asMemory' => [],
            'entities' => [],
            'stages' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'queryParams[]' => ParamOption::class,
            'memoryParams[]' => ParamOption::class,
            'asStage' => StageMeta::class,
            'stages[]' => StageMeta::class,
        ];
    }

    public function _filter(array $data): array
    {
//        $name = $data['name'] ?? '';
//        $asStage = $data['asStage'] ?? [];
//        $asStage['contextName'] = $name;
//        $data['asStage'] = $asStage;
//
//        $asMemory = $data['asMemory'] ?? [];
//        $asMemory['name'] = $name;
//        $data['asMemory'] = $asMemory;
//
//        $data['stages'] = array_map(function($stage) use ($name) {
//            $stage['contextName'] = $name;
//            return $stage;
//        }, $data['stages'] ?? []);

        return parent::_filter($data);
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        $name = $data['name'] ?? '';

        if (!ContextUtils::isValidContextName($name)) {
            return "contextName $name is invalid";
        }

        return parent::validate($data);
    }


    /*------ properties -------*/

    public function getName(): string
    {
        return $this->_name;
    }

    public function getTitle(): string
    {
        return $this->_title;
    }

    public function getDescription(): string
    {
        return $this->_desc;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /*------ parameters -------*/

    public function getQueryParams(): ParamDefCollection
    {
        return $this->_queries
            ?? $this->_queries = new IDefParamCollection($this->queryParams);
    }

    public function getEntityParams(): ParamDefCollection
    {
        if (isset($this->_entities)) {
            return $this->_entities;
        }

        $params = $this->asMemoryDef()->getParams();

        return $this->_entities = new IDefParamCollection(
            array_map(function(string $name) use ($params): ParamDef {
                return $params->getParam($name);
            }, $this->entities)
        );
    }

    public function getEntityNames(): array
    {
        return $this->entities;
    }

    public function getParams(): ParamDefCollection
    {
        if (isset($this->_params)) {
            return $this->_params;
        }
        $params = $this->getQueryParams()->getAllParams();
        $params = array_merge(
            $params,
            $this->asMemoryDef()->getParams()->getAllParams()
        );

        return $this->_params = $params;
    }


    /*------ relations -------*/

    public function asMemoryDef() : MemoryDef
    {
        return $this->_asMemory
            ?? $this->_asMemory = $this->asMemory->getWrapper();
    }

    public function asStageDef() : StageDef
    {
        return $this->_asStage
            ?? $this->_asStage = $this->asStage->getWrapper();
    }


    /*------ to context -------*/

    public function wrapContext(Cloner $cloner, Ucl $ucl): Context
    {
        $wrapper = $this->contextWrapper ?? IContext::class;
        return new $wrapper($ucl, $cloner);
    }

    /*------ meta -------*/

    public function getMeta(): Meta
    {
        $data = $this->toArray();
        $name = $data['name'] ?? '';
        $title = $data['title'] ?? '';
        $desc = $data['desc'] ?? '';

        unset($data['name']);
        unset($data['title']);
        unset($data['desc']);

        return new ContextMeta([
            'name' => $name,
            'title' => $title,
            'desc' => $desc,
            'config' => $data,
        ]);
    }

    /**
     * @param ContextMeta $meta
     * @return Wrapper
     */
    public static function wrap(Meta $meta): Wrapper
    {
        if ($meta instanceof ContextMeta) {
            throw new InvalidArgumentException(
                'accept ' . ContextMeta::class . ' only'
            );
        }

        return new static(
            $meta->name,
            $meta->title,
            $meta->desc,
            $meta->config
        );
    }



    /*------ stages -------*/

    protected function getStageMetaMap() : Collection
    {
        return $this->_stageMap
            ?? $this->_stageMap = new Collection(array_reduce(
                $this->stages,
                function($output, StageMeta $stage) {
                    $output[$stage->name] = $stage;
                    return $output;
                },
                [$this->asStage]
            ));
    }

    public function hasStage(string $stageName): bool
    {
        if ($stageName === '') {
            return true;
        }

        return $this->getStageMetaMap()->has($stageName);
    }

    public function getPredefinedStage(string $stageName): StageDef
    {
        $map = $this->getStageMetaMap();
        if (!$map->has($stageName)) {
            throw new DefNotDefinedException(StageMeta::class, $stageName);
        }
        return $map->get($stageName);
    }

    public function getPredefinedStageNames(bool $isFullname = false): array
    {
        return $this
            ->getStageMetaMap()
            ->map(function(StageMeta $meta) use ($isFullname){
                if ($isFullname) {
                    return $meta->getFullStageName();
                }

                return $meta->name;
            })
            ->all();
    }

    public function __destruct()
    {
        $this->_asStage = null;
        $this->_asMemory = null;
        $this->_entities = null;
        $this->_queries = null;
        $this->_stageMap = null;
        $this->_params = null;
        parent::__destruct();
    }

}