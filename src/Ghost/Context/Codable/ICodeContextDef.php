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
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindDef\ParamDefCollection;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IMindDef\IMemoryDef;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICodeContextDef implements ContextDef
{

    /*-------- config --------*/

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $contextClass;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $desc;

    /**
     * @var array
     */
    protected $config = [
        // 'priority' => 0,
        // 'scopes' => [],
        // 'comprehendPipes' => []
    ];

    /*-------- cached properties --------*/

    /**
     * @var ParamDefCollection
     */
    protected $query;

    /**
     * @var ParamDefCollection
     */
    protected $params;

    /**
     * @var string[]
     */
    protected $entities = [];

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @var string[]
     */
    protected $scopes = [];

    /**
     * @var ParamDefCollection
     */
    protected $entityParams;

    /**
     * @var null|string[]
     */
    protected $comprehendPipes;

    /**
     * @var StageDef[]
     */
    protected $stages = [];

    /*-------- construct --------*/

    public function __construct(string $contextClass, ContextMeta $meta = null)
    {
        $this->contextClass = $contextClass;

        if (isset($meta)) {
            $this->initMeta($meta);
        }

        $this->initialize();
    }

    protected function initMeta(ContextMeta $meta) : void
    {
        $this->contextName = $meta->name;
        $this->config = $meta->config;
        $this->title = $meta->title;
        $this->desc = $meta->desc;
    }

    /*-------- initialize --------*/

    protected function initialize() : void
    {
        $creator = new CodeDefCreator($this->contextClass);

        // query
        $this->query = $creator->getQueryParams();
        $configs = $creator->getConfig();

        // params
        $this->params = $creator->getParams();

        // entity params
        $entityBuilder = $creator->getEntityBuilder($this->contextName);
        $this->entityParams = $entityBuilder->getEntityParams();
        foreach ($this->entityParams->getAllParams() as $param) {
            $this->params->addParamDef($param);
        }

        // entity stages
        $stages = $entityBuilder->getEntityStages();
        foreach ($stages as $stage) {
            $id = $stage->getStageShortName();
            $this->stages[$id] = $stage;
        }

        // method stages
        $stages = $creator->getMethodStages();
        foreach ($stages as $stage) {
            $id = $stage->getStageShortName();
            $this->stages[$id] = $stage;
        }
        unset($stages);


        // priority
        $this->priority = $this->config[DefineConfig::KEY_PRIORITY]
            ?? $configs[DefineConfig::KEY_PRIORITY]
            ?? 0;

        // scopes
        $this->scopes = $this->config[DefineConfig::KEY_SCOPES]
            ?? $configs[DefineConfig::KEY_SCOPES]
            ?? [];

        // pipes
        $this->comprehendPipes = $this->config[DefineConfig::KEY_COMPREHEND_PIPES]
            ?? $configs[DefineConfig::KEY_COMPREHEND_PIPES]
            ?? [];

        // title
        $this->title = empty($this->title)
            ? (
                $this->config[DefineConfig::KEY_TITLE]
                ?? $configs[DefineConfig::KEY_TITLE]
                ?? ''
            )
            : $this->title;

        // desc
        $this->desc = empty($this->desc)
            ? (
                $this->config[DefineConfig::KEY_DESC]
                ?? $configs[DefineConfig::KEY_DESC]
                ?? ''
            )
            : $this->desc;
    }

    /*-------- contextDef --------*/



    public function wrapContext(Cloner $cloner, Ucl $ucl): Context
    {
        $contextClass = $this->contextClass;
        return call_user_func(
            [$contextClass, CodeContext::WRAP_CONTEXT_FUNC],
            $cloner,
            $ucl
        );
    }

    public function getQueryParams(): ParamDefCollection
    {
        return $this->query;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function comprehendPipes(Dialog $current): ? array
    {
        return $this->comprehendPipes;
    }

    public function getEntityParams(): ParamDefCollection
    {
        return $this->entityParams;
    }

    public function getParams(): ParamDefCollection
    {
        return $this->params;
    }

    /*-------- stage --------*/

    public function getStage(string $stageName): StageDef
    {
        if (isset($this->stages[$stageName])) {
            return $this->stages[$stageName];
        }

        throw new DefNotDefinedException(
            StageDef::class,
            $stageName
        );
    }

    public function getStageNames(bool $isFullname = false): array
    {
        $names = array_keys($this->stages);

        if ($isFullname) {
            $contextName = $this->contextName;
            return array_map(
                function(string $stageName) use ($contextName) {
                    return ContextUtils::makeFullStageName(
                        $contextName,
                        $stageName
                    );
                },
                $names
            );
        }

        return $names;
    }


    /*-------- def --------*/

    public function getName(): string
    {
        return $this->contextName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->desc;
    }

    /*-------- def --------*/
    public function asStageDef(): StageDef
    {
        // TODO: Implement asStageDef() method.
    }

    public function asMemoryDef(): MemoryDef
    {
        $meta = new MemoryMeta([
            'name' => $this->contextName,
            'title' => $this->title,
            'desc' => $this->desc,
            'scopes' => $this->scopes,
            'params' => $this->params->getAllParams()
        ]);

        return new IMemoryDef($meta);
    }


    /*-------- config --------*/

    public function getConfig() : array
    {
        return $this->config;
    }

    /*-------- wrapper --------*/

    public function getMeta(): Meta
    {
        return new ContextMeta([
            'name' => $this->contextName,
            'title' => $this->title,
            'desc' => $this->desc,
            'wrapper' => $this->contextClass,
            'config' => $this->getConfig(),
        ]);
    }

    /**
     * @param ContextMeta $meta
     * @return Wrapper
     */
    public static function wrap(Meta $meta) : Wrapper
    {
        $wrapper = $meta->wrapper;
        return call_user_func([$wrapper, CodeContext::MAKE_DEF_FUNC], $meta);
    }

    /*-------- magic --------*/

    public function __get($name)
    {
        // TODO: Implement __get() method.
    }

    public function __isset($name)
    {
        // TODO: Implement __isset() method.
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}