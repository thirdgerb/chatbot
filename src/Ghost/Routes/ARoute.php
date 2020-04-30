<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Routes;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routes\Activate\ActivateStage;
use Commune\Blueprint\Ghost\Routes\Route;
use Commune\Blueprint\Ghost\Routing\Matcher;
use Commune\Blueprint\Ghost\Snapshot\Task;
use Commune\Ghost\Operators\Stage\Activation;
use Commune\Ghost\Operators\DoFulfill;
use Commune\Ghost\Stage\IMatcher;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ARoute implements Route
{
    use TInjectable;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var Task
     */
    protected $selfTask;

    /**
     * @var Context
     */
    protected $selfContext;

    /**
     * ARoute constructor.
     * @param Cloner $cloner
     * @param Task $selfTask
     */
    public function __construct(Cloner $cloner, Task $selfTask)
    {
        $this->cloner = $cloner;
        $this->selfTask = $selfTask;
    }


    public function make(string $abstract, array $parameters = [])
    {
        $parameters = $parameters + $this->getContextualInjections();
        // 容器
        $container = $this->cloner->container;
        return $container->make($abstract, $parameters);
    }

    public function call($caller, array $parameters = [])
    {
        $parameters = $parameters + $this->getContextualInjections();
        // 容器
        $container = $this->cloner->container;
        return $container->call($caller, $parameters);
    }


    abstract protected function getInjectableParams() : array;


    public function getContextualInjections(): array
    {
        $parameters = [];
        $injectable = $this->getInjectableParams();

        // 准备好各种依赖注入.
        foreach ($injectable as $key => $object) {
            $parameters[$key] = $object;

            if ($object instanceof Injectable) {
                foreach ($object->getInterfaces() as $interface) {
                    $parameters[$interface] = $object;
                }
            }
        }

        // 可以用 $dependencies 来查看可以依赖注入的对象.
        $parameters['dependencies'] = array_keys($parameters);
        return $parameters;
    }

    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Route::class);
    }

    /*-------- router --------*/

    public function matcher(): Matcher
    {
        return new IMatcher(
            $this->cloner,
            $this->cloner->ghostInput->getMessage()
        );
    }

    public function restart(bool $reset = false): Operator
    {
        if ($reset) {
            $context = $this->self;
            $context->resetData();
        }
        // 重置所有的路径.
        $this->selfTask->reset();
        return new Activation($this->selfTask, ActivateStage::class);
    }

    public function next(...$stageNames): Operator
    {
        if (!empty($stageNames)) {
            $this->selfTask->forward($stageNames);
        }

        if ($this->selfTask->next()) {
            return new Activation($this->selfTask, ActivateStage::class);
        }

        return new DoFulfill($this->selfTask);
    }

    public function swerve(...$stageNames): Operator
    {
        $this->selfTask->reset();
        if (!empty($stageNames)) {
            $this->selfTask->forward($stageNames);
        }
        return $this->next();
    }


    /*-------- getter --------*/

    public function __get($name)
    {
        if ($name === 'self') {
            return $this->selfContext
                ?? $this->selfContext = $this->selfTask->findContext($this->cloner);
        }

        return null;
    }
}