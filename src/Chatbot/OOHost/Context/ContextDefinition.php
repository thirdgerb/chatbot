<?php


namespace Commune\Chatbot\OOHost\Context;

use Closure;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Entities\DependingBuilder;
use ReflectionMethod;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Listeners\ExitingCatcher;
use Commune\Chatbot\OOHost\Exceptions\NavigatorException;
use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\OOHost\Context\Stages\CallbackStage;
use Commune\Chatbot\OOHost\Context\Stages\StartStage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Illuminate\Support\Str;

class ContextDefinition implements Definition
{
    const ACCEPT_CLAZZ = Context::class;

    /**
     * @var string
     */
    protected $contextClazz;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var Closure[]
     */
    protected $stageMethods = [];

    /**
     * @var Entity[]
     */
    protected $entities = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * @var null|Closure
     */
    protected $contextMaker;

    public function __construct(
        string $contextName,
        string $contextClazz,
        string $description = '',
        Closure $contextMaker = null
    )
    {
        if (!is_a($contextClazz, static::ACCEPT_CLAZZ, TRUE)) {
            throw new ConfigureException(
                static::class
                . ' not accept class name as ' . $contextClazz
            );
        }

        $this->contextName = StringUtils::namespaceSlashToDot($contextName);
        $this->contextClazz = $contextClazz;
        $this->description = $description;
        $this->contextMaker = $contextMaker;

        $this->initialize();

    }

    protected function initialize() : void
    {
        $this->registerMethodAsStage(Context::INITIAL_STAGE);
        $this->registerClassStage();
        $this->registerDepend();
    }

    /**
     * 按先后顺序传入参数.
     * 此方法通常给系统来使用.
     *
     * @param mixed ...$args
     * @return Context
     * @throws
     */
    public function newContext(...$args): Context
    {
        if (isset($this->contextMaker)) {
            $data = call_user_func_array($this->contextMaker, $args);
        } else {

            //  never throw exception
            $reflection = new \ReflectionClass($this->contextClazz);
            $data = $reflection->newInstanceArgs($args);
        }

        if (!is_a($data, Context::class, TRUE)) {
            // never throw
            throw new ConfigureException(
                'context definition of '
                . $this->contextName
                . ' register class ' . $this->contextClazz
                . ' can not construct to instance of ' . Context::class
                . ', why ?'
            );
        }

        /**
         * @var Context $data
         */
        return $data;
    }




    /**
     * 注意, contextName 的反斜杠会被转化为 dot
     * @return string
     */
    public function getName(): string
    {
        return $this->contextName;
    }

    public function getClazz(): string
    {
        return $this->contextClazz;
    }

    public function getDesc(): string
    {
        return $this->description;
    }


    public function registerMethodAsStage(string $stageName) : void
    {
        $methodName = Context::STAGE_METHOD_PREFIX . ucfirst($stageName);

        $this->setStage($stageName, function(Stage $stageRoute) use ($methodName) {
            return call_user_func(
                [$stageRoute->self, $methodName],
                $stageRoute
            );
        });
    }



    public function addEntity(Entity $entity): void
    {
        $this->entities[$entity->name] = $entity;
        //为避免歧义, entity 的优先级比 method 或者其它方式定义的stageMethod 要高.
        $this->setStage($entity->name, [$entity, Entity::STAGE_METHOD]);
    }

    public function getEntityNames(): array
    {
        return array_keys($this->entities);
    }


    public function hasEntity(string $entityName): bool
    {
        return isset($this->entities[$entityName]);
    }

    public function getEntity(string $entityName): ? Entity
    {
        return $this->entities[$entityName] ?? null;
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function hasStage(string $stageName): bool
    {
        return isset($this->stageMethods[$stageName]);
    }


    public function setStage(string $stage, callable $builder): void
    {
        $this->stageMethods[$stage] = $builder;
    }

    public function depends(Context $instance): array
    {
        $results = [];
        foreach ($this->entities as $entity) {
            if (!$entity->isPrepared($instance)) {
                $results[] = $entity;
            }
        }
        return $results;
    }

    public function depending(Context $instance): ? Entity
    {
        foreach ($this->entities as $entity) {
            if (!$entity->isPrepared($instance)) {
                return $entity;
            }
        }
        return null;
    }


    public function getStageNames() : array
    {
        return array_unique(
            array_keys($this->stageMethods),
            array_keys($this->entities)
        );
    }

    public function startStage(
        Context $self,
        Dialog $dialog,
        string $stage
    ): Navigator
    {
        $this->checkStageExists($stage);
        // 检查depending
        if ($stage === Context::INITIAL_STAGE) {
            $entity = $this->depending($self);
            if (!empty($entity)) {
                return $dialog->goStagePipes(
                    [ $entity->name, $stage ],
                    true
                );
            }
        }

        // 正常启动.
        // 理论上不会有死循环.
        $stageRoute = new StartStage(
            $stage,
            $self,
            $dialog
        );

        return $this->callStage($stage, $stageRoute);
    }

    public function callbackStage(
        Context $self,
        Dialog $dialog,
        string $stage,
        Message $callbackValue = null
    ): Navigator
    {
        $this->checkStageExists($stage);

        $stageRoute = new CallbackStage(
            $stage,
            $self,
            $dialog,
            $callbackValue
        );

        return $this->callStage($stage, $stageRoute);
    }

    public function callStage(string $stage, Stage $stageRoute) : Navigator
    {
        $caller = $this->stageMethods[$stage];

        try {

            $context = $stageRoute->self;
            $method = Context::STAGE_MIDDLEWARE_METHOD;
            if (method_exists($context, $method)) {
                $context->{$method}($stageRoute);
            }

            $result = call_user_func($caller, $stageRoute);

        } catch (NavigatorException $e) {
            return $e->getNavigator();

        }

        if (!$result instanceof Navigator) {
            throw new LogicException(
                'context ' . $this->contextName
                . ' stage ' . $stage
                . ' should only return value instance of '. Navigator::class
                . ', ' . gettype($result) . ' given'
            );
        }

        return $result;
    }


    protected function checkStageExists(string $stage) : void
    {
        if (!isset($this->stageMethods[$stage])) {
            throw new ConfigureException(
                'context ' . $this->contextName
                . ' stage ' . $stage
                . ' not found while call it'
            );
        }
    }


    public function onExiting(
        int $exiting,
        Context $self,
        Dialog $dialog,
        Context $callback =null
    ): ? Navigator
    {

        $exiting = new ExitingCatcher($exiting, $self, $dialog, $callback);
        $method = Context::EXITING_LISTENER;


        call_user_func([$self, $method], $exiting);

        return $exiting->navigator;
    }


    public function __get($name)
    {
        return $this->{$name};
    }


    /**
     * @throws
     */
    protected function registerClassStage() : void
    {
        $reflection = new \ReflectionClass($this->contextClazz);
        $this->registerStageMethod($reflection->getMethod(
            Context::STAGE_METHOD_PREFIX. Context::INITIAL_STAGE
        ));

        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $name = $method->getName();

            // stage method
            if (Str::startsWith(
                $name,
                Context::STAGE_METHOD_PREFIX)
            ) {
                $this->registerStageMethod($method);
            }
        }

    }

    protected function registerStageMethod(ReflectionMethod $method) : void
    {
        $methodName = $method->getName();
        $stageName = lcfirst(str_replace(
            Context::STAGE_METHOD_PREFIX,
            '',
            $methodName
        ));

        if (empty($stageName)) {
            return;
        }

        // 校验逻辑
        // 校验参数数量.
        $parameters = $method->getParameters();
        if (count($parameters) > 1) {
            $this->invalidClassStageMethod($methodName);
        }

        // 检查type hint
        $stageParam = $parameters[0];
        if ($stageParam->hasType() && $stageParam->getType() != Stage::class) {
            $this->invalidClassStageMethod($methodName);
        }

        // 返回参数校验.
        if ($method->hasReturnType() && $method->getReturnType() != Navigator::class) {
            $this->invalidClassStageMethod($methodName);
        }


        $this->registerMethodAsStage($stageName);
    }

    protected function invalidClassStageMethod(string $methodName) :void
    {
        throw new ConfigureException(
            'context stage method '
            . $methodName . ' is invalid, should only have 1 parameter,'
            .' type hint must be '. Stage::class .' or none,'
            .' return type must be '.Navigator::class . ' or none'
        );

    }

    protected function registerDepend() : void
    {
        $builder = new DependingBuilder($this);
        $method = [$this->contextClazz, Context::DEPENDENCY_BUILDER];
        call_user_func($method, $builder);
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        $var = $this->contextClazz . '::CONTEXT_TAGS';
        if (defined($var)) {
            return constant($var);
        }
        return [];
    }


}