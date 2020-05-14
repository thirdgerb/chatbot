<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\MindDef\DefParam;
use Commune\Blueprint\Ghost\MindDef\DefParamsCollection;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IMindDef\IMemoryDef;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
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
 *
 * @property-read string $name          语境的名称
 * @property-read string $title         语境的标题
 * @property-read string $desc          语境的简介
 * @property-read int $priority         语境的默认优先级
 * @property-read bool $public
 *
 * ## context wrapper
 * @property-read string|null $contextWrapper       Context 的包装器.
 *
 * ## 属性相关
 * @property-read ParamOption[] $queryParams
 * @property-read string[] $entities
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
     * @var DefParamsCollection
     */
    protected $_queries;

    /**
     * @var DefParamsCollection
     */
    protected $_entities;

    /**
     * @var DefParamsCollection
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

    public static function stub(): array
    {
        return [

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
        $name = $data['name'] ?? '';
        $asStage = $data['asStage'] ?? [];
        $asStage['contextName'] = $name;
        $data['asStage'] = $asStage;

        $asMemory = $data['asMemory'] ?? [];
        $asMemory['name'] = $name;
        $data['asMemory'] = $asMemory;

        $data['stages'] = array_map(function($stage) use ($name) {
            $stage['contextName'] = $name;
            return $stage;
        }, $data['stages'] ?? []);

        return parent::_filter($data);
    }


    /*------ properties -------*/

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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    /*------ parameters -------*/
    public function getQueryParams(): DefParamsCollection
    {
        return $this->_queries
            ?? $this->_queries = new IDefParamCollection($this->queryParams);
    }

    public function getEntityParams(): DefParamsCollection
    {
        if (isset($this->_entities)) {
            return $this->_entities;
        }

        $params = $this->asMemoryDef()->getParams();

        return $this->_entities = new IDefParamCollection(
            array_map(function(string $name) use ($params): DefParam {
                return $params->getParam($name);
            }, $this->entities)
        );
    }

    public function getEntityNames(): array
    {
        return $this->entities;
    }

    public function getParams(): DefParamsCollection
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
        $config = $meta->config;
        $config['name'] = $meta->name;
        $config['title'] = $meta->title;
        $config['desc'] = $meta->desc;

        return new static($config);
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

    public function getStage(string $stageName): StageDef
    {
        $map = $this->getStageMetaMap();
        if (!$map->has($stageName)) {
            throw new DefNotDefinedException(
                __METHOD__,
                StageMeta::class,
                $stageName
            );
        }
        return $map->get($stageName);
    }

    public function getStageNames(bool $isFullname = false): array
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