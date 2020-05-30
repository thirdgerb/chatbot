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
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\Context\ParamCollection;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\Params\IParamCollection;
use Commune\Ghost\IMindDef\IMemoryDef;
use Commune\Ghost\Stage\InitStage;
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
     * @var Context\ContextOption
     */
    protected $configOption;

    /*-------- cached properties --------*/

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var array
     */
    protected $scopes;

    /**
     * @var array
     */
    protected $properties = [];


    /**
     * @var Context\ParamCollection
     */
    protected $queryDefaults;


    /**
     * @var Context\ParamCollection
     */
    protected $paramDefaults;


    /**
     * @var string[]
     */
    protected $depending;

    /**
     * @var string[]
     */
    protected $entityNames;

    /**
     * @var string
     */
    protected $firstStage;


    /**
     * @var StageDef[]
     */
    protected $stages = [];

    /*-------- construct --------*/

    public function __construct(string $contextClass, ContextMeta $meta = null)
    {
        $this->contextClass = $contextClass;

        if (isset($meta)) {
            $this->title = $meta->title;
            $this->desc = $meta->desc;
            $this->configOption = new Context\ContextOption($meta->config);
        }

        $this->contextName = isset($meta)
            ? $meta->name
            : ContextUtils::normalizeContextName($contextClass);

        $this->initialize();
    }

    /*-------- initialize --------*/

    protected function initialize() : void
    {
        $creator = new CodeDefCreator($this->contextClass);

        $this->configOption = $this->configOption ?? $creator->getConfig();
        $this->title = $this->configOption->title;
        $this->desc = $this->configOption->desc;

        foreach ($creator->eachMethodStage() as $stage) {
            /**
             * @var StageDef $stage
             */
            $this->stages[$stage->getName()] = $stage;
        }
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

    public function getPriority(): int
    {
        return $this->priority
            ?? $this->priority = $this->configOption->priority ?? 0;
    }

    public function getScopes(): array
    {
        return $this->scopes
            ?? $this->scopes = $this->configOption->memoryScopes ?? [];
    }

    public function comprehendPipes(Dialog $current): ? array
    {
        $name = __FUNCTION__;

        return array_key_exists($name, $this->properties)
            ? $this->properties[$name]
            : (
                $this->properties[$name] = $this->configOption->comprehendPipes ?? null
            );
    }

    public function getQueryDefaults(): ParamCollection
    {
        return $this->queryDefaults
            ?? $this->queryDefaults = new IParamCollection(
                $this->configOption->queryDefaults ?? []
            );
    }

    public function getParamsDefaults(): ParamCollection
    {
        return $this->paramDefaults
            ?? $this->paramDefaults = new IParamCollection(
                $this->configOption->paramDefaults ?? []
            );
    }

    public function getDependingNames(): array
    {
        return $this->depending
            ?? $this->depending = $this->configOption->dependingNames ?? [];
    }

    public function getEntityNames(): array
    {
        return $this->entityNames
            ?? $this->entityNames = $this->configOption->entityNames ?? [];
    }


    /*-------- config --------*/

    public function firstStage(): ? string
    {
        return $this->firstStage
            ?? $this->configOption->firstStage
            ?? CodeContext::FIRST_STAGE;
    }

    public function onCancelStage(): ? string
    {
        $name = __FUNCTION__;
        return array_key_exists($name, $this->properties)
            ? $this->properties[$name]
            : (
                $this->properties[$name] = $this->configOption->onCancel ?? null
            );
    }

    public function onQuitStage(): ? string
    {
        $name = __FUNCTION__;
        return array_key_exists($name, $this->properties)
            ? $this->properties[$name]
            : (
            $this->properties[$name] = $this->configOption->onQuit ?? null
            );
    }

    public function commonStageRoutes(): array
    {
        $name = __FUNCTION__;
        return array_key_exists($name, $this->properties)
            ? $this->properties[$name]
            : (
                $this->properties[$name] = $this->configOption->stageRoutes ?? []
            );
    }

    public function commonContextRoutes(): array
    {
        $name = __FUNCTION__;
        return array_key_exists($name, $this->properties)
            ? $this->properties[$name]
            : (
            $this->properties[$name] = $this->configOption->contextRoutes ?? []
            );
    }

    /*-------- stage --------*/

    public function getPredefinedStage(string $stageName): StageDef
    {
        if ($stageName === '') {
            return $this->asStageDef();
        }

        if (isset($this->stages[$stageName])) {
            return $this->stages[$stageName];
        }

        throw new DefNotDefinedException(
            StageDef::class,
            $stageName
        );
    }

    public function getPredefinedStageNames(bool $isFullname = false): array
    {
        $names = array_keys($this->stages);
        array_unshift($names, '');

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
        $contextName = $this->getName();
        return new InitStage(
            $contextName,
            $contextName,
            $this->getTitle(),
            $this->getDescription(),
            $this->configOption->asIntent ??  []
        );
    }

    public function asMemoryDef(): MemoryDef
    {
        $meta = new MemoryMeta([
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'desc' => $this->getDescription(),
            'scopes' => $this->getScopes(),
            'params' => $this->configOption->paramDefaults ?? []
        ]);

        return new IMemoryDef($meta);
    }


    /*-------- config --------*/

    public function getConfig() : array
    {
        return $this->configOption->toArray();
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

    public function __destruct()
    {
        $this->stages = [];
        $this->configOption = null;
    }
}