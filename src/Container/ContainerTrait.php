<?php

/**
 * Class ContainerTrait
 * @package Container
 */

namespace Commune\Container;


use Closure;
use Exception;
use LogicException;
use ReflectionClass;
use ReflectionParameter;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Contracts\Container\BindingResolutionException;


/**
 * 用trait 快速生成一个容器, 共享注册, 实例分割.
 *
 * Class ContainerTrait
 * @package Container
 * @mixin ContainerContract
 *
 * @see \Illuminate\Container\Container
 */
trait ContainerTrait
{

   /**
    * @var array
    */
   protected $shared = [];

   /**
    * The stack of concretions currently being built.
    *
    * @var array[]
    */
   protected $buildStack = [];

   /**
    * The parameter override stack.
    *
    * @var array[]
    */
   protected $with = [];

   /**
    * The container's bindings.
    *
    * @var array[]
    */
   private static $bindings = [];


   /**
    * The container's shared instances.
    *
    * @var \object[]
    */
   private static $instances = [];

   /**
    * The registered type aliases.
    *
    * @var string[]
    */
   private static $aliases = [];

   /**
    * @var Closure[][]
    */
   private static $extenders = [];

    /**
    * @param string $abstract
    * @return bool
    */
   public function bound(string $abstract): bool
   {
       return array_key_exists($abstract, self::$bindings)
           || array_key_exists($abstract, self::$instances)
           || array_key_exists($abstract, $this->shared)
           || $this->isAlias($abstract);
   }

   /**
    * @param string $abstract
    * @param null $concrete
    * @param bool $shared
    */
   public function bind(string $abstract, $concrete = null, bool $shared = false): void
   {
       $this->dropStaleInstances($abstract);

       // If no concrete type was given, we will simply set the concrete type to the
       // abstract type. After that, the concrete type to be registered as shared
       // without being forced to state their classes in both of the parameters.
       if (is_null($concrete)) {
           $concrete = $abstract;
       }

       // If the factory is not a Closure, it means it is just a class name which is
       // bound into this container to the abstract type and we will just wrap it
       // up inside its own Closure to give us more convenience when extending.
       if (! $concrete instanceof Closure) {
           $concrete = $this->getClosure($abstract, $concrete);
       }

       self::$bindings[$abstract] = compact('concrete', 'shared');

       // 去掉了上下文逻辑.
       // If the abstract type was already resolved in this container we'll fire the
       // rebound listener so that any objects which have already gotten resolved
       // can have their copy of the object updated via the listener callbacks.
       // if ($this->resolved($abstract)) {
       //     $this->rebound($abstract);
       // }
   }


   /**
    * Register a binding if it hasn't already been registered.
    *
    * @param  string  $abstract
    * @param  \Closure|string|null  $concrete
    * @param  bool  $shared
    * @return void
    */
   public function bindIf(string $abstract, $concrete = null, bool $shared = false) : void
   {
       if (! $this->bound($abstract)) {
           $this->bind($abstract, $concrete, $shared);
       }
   }


   /**
    * Register a shared binding in the container.
    *
    * @param  string  $abstract
    * @param  \Closure|string|null  $concrete
    * @return void
    */
   public function singleton(string $abstract, $concrete = null): void
   {
       $this->bind($abstract, $concrete, true);
   }



   /**
    * share existing instance as shared in the container.
    *
    * @param  string  $abstract
    * @param  mixed   $instance
    * @return mixed
    */
   public function share(string $abstract, $instance)
   {
       unset(self::$aliases[$abstract]);
       $this->shared[$abstract] = $instance;

       return $instance;
   }


   public function instance(string $abstract, $instance)
   {
       // $isBound = $this->bound($abstract);
       unset(self::$aliases[$abstract]);

       // We'll check to determine if this type has been bound before, and if it has
       // we will fire the rebound callbacks registered with the container and it
       // can be updated with consuming classes that have gotten resolved here.
       unset($this->shared[$abstract]);
       self::$instances[$abstract] = $instance;

       // 去掉了所有事件.
       // if ($isBound) {
       //     $this->rebound($abstract);
       // }

       return $instance;
   }


   /**
    * Get a closure to resolve the given type from the container.
    *
    * @param  string  $abstract
    * @return \Closure
    */
   public function factory(string $abstract): Closure
   {
       return function () use ($abstract) {
           return $this->make($abstract);
       };
   }


   /**
    * Flush the container of all bindings and resolved instances.
    *
    * @return void
    */
   public function flush() : void
   {
       $this->flushContainer();
       $this->flushInstance();
   }

   public static function flushContainer() : void
   {
       self::$aliases = [];
       self::$bindings = [];
       self::$instances = [];
   }

   protected function flushInstance() : void
   {
       $this->shared = [];
       $this->buildStack = [];
       $this->with = [];
   }

   /**
    * @param string $abstract
    * @param array $parameters
    * @return mixed
    * @throws BindingResolutionException
    */
   public function make(string $abstract, array $parameters = [])
   {
       return $this->resolve($abstract, $parameters);
   }


    /**
     * @param callable $caller
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    public function call(callable $caller, array $parameters = [])
    {
        return BoundMethod::call($this, $caller, $parameters);
    }


    /**
    * @param string $abstract
    * @param array $parameters
    * @return mixed
    * @throws BindingResolutionException
    */
   public function resolve(string $abstract, array $parameters = [])
   {
       $abstract = $this->getAlias($abstract);

       $needsContextualBuild = ! empty($parameters);

       // shared
       if (isset($this->shared[$abstract])) {
           return $this->shared[$abstract];
       }

       // If an instance of the type is currently being managed as a singleton we'll
       // just return an existing instance instead of instantiating new instances
       // so the developer can keep using the same objects instance every time.
       if (isset(self::$instances[$abstract]) && ! $needsContextualBuild) {
           return self::$instances[$abstract];
       }

       $this->with[] = $parameters;

       $concrete = $this->getConcrete($abstract);

       // We're ready to instantiate an instance of the concrete type registered for
       // the binding. This will instantiate the types, as well as resolve any of
       // its "nested" dependencies recursively until all have gotten resolved.
       if ($this->isBuildable($concrete, $abstract)) {
           $object = $this->build($concrete);
       } else {
           $object = $this->make($concrete);
       }

       // If we defined any extenders for this type, we'll need to spin through them
       // and apply them to the object being built. This allows for the extension
       // of services, such as changing configuration or decorating the object.
       foreach ($this->getExtenders($abstract) as $extender) {
           $object = $extender($object, $this);
       }

       // If the requested type is registered as a singleton we'll want to cache off
       // the instances in "memory" so we can return it later without creating an
       // entirely new instance of an object on each subsequent request for it.
       if ($this->isShared($abstract) && ! $needsContextualBuild) {
           $this->shared[$abstract] = $object;
       }

       // 停止事件
       // if ($raiseEvents) {
       //     $this->fireResolvingCallbacks($abstract, $object);
       // }

       // Before returning, we will also set the resolved flag to "true" and pop off
       // the parameter overrides for this build. After those two things are done
       // we will be ready to return back the fully constructed class instance.
       // $this->resolved[$abstract] = true;

       array_pop($this->with);

       return $object;
   }

   /**
    * @param $id
    * @return mixed
    * @throws BindingResolutionException
    * @throws EntryNotFoundException
    */
   public function get($id)
   {
       try {
           return $this->make($id);
       } catch (Exception $e) {
           if ($this->has($id)) {
               throw new BindingResolutionException('', 0, $e);
           }

           throw new EntryNotFoundException('', 0, $e);
       }
   }

   /**
    *  {@inheritdoc}
    */
   public function has($id)
   {
       return $this->bound($id);
   }

    /**
    * Get the alias for an abstract if available.
    *
    * @param  string  $abstract
    * @return string
    */
   public function getAlias(string $abstract) : string
   {
       if (! isset(self::$aliases[$abstract])) {
           return $abstract;
       }

       return $this->getAlias(self::$aliases[$abstract]);
   }

    /**
     * Alias a type to a different name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     *
     * @throws \LogicException
     */
    public function alias(string $abstract, string $alias): void
    {
        if ($alias === $abstract) {
            throw new LogicException("[{$abstract}] is aliased to itself.");
        }

        self::$aliases[$alias] = $abstract;
    }

   /**
    * Determine if a given string is an alias.
    *
    * @param  string  $name
    * @return bool
    */
   protected function isAlias($name)
   {
       return array_key_exists($name, self::$aliases);
   }

   /**
    * Drop all of the stale instances and aliases.
    *
    * @param  string  $abstract
    * @return void
    */
   protected function dropStaleInstances(string $abstract) : void
   {
       unset(
           self::$instances[$abstract],
           self::$aliases[$abstract],
           $this->shared[$abstract]
       );
   }


   /**
    * Get the Closure to be used when building a type.
    *
    * @param  string  $abstract
    * @param  string  $concrete
    * @return \Closure
    */
   protected function getClosure(string $abstract, string $concrete) : Closure
   {
       return function ($container, $parameters = []) use ($abstract, $concrete) {
           /**
            * @var ContainerTrait $container
            */
           if ($abstract == $concrete) {
               return $container->build($concrete);
           }

           return $container->make($concrete, $parameters);
       };
   }



   /**
    * Instantiate a concrete instance of the given type.
    *
    * @param string|Closure $concrete
    * @return mixed
    * @throws BindingResolutionException
    */
   public function build($concrete)
   {
       // If the concrete type is actually a Closure, we will just execute it and
       // hand back the results of the functions, which allows functions to be
       // used as resolvers for more fine-tuned resolution of these objects.
       if ($concrete instanceof Closure) {
           return $concrete($this, $this->getLastParameterOverride());
       }

       if (!class_exists($concrete)) {
           $this->notInstantiable($concrete, "class not exists");
           return null;
       }

       $reflector = new ReflectionClass($concrete);
       // If the type is not instantiable, the developer is attempting to resolve
       // an abstract type such as an Interface or Abstract Class and there is
       // no binding registered for the abstractions so we need to bail out.
       if (! $reflector->isInstantiable()) {
           $this->notInstantiable($concrete);
           // throws
           return null;
       }

       $this->buildStack[] = $concrete;

       $constructor = $reflector->getConstructor();

       // If there are no constructors, that means there are no dependencies then
       // we can just resolve the instances of the objects right away, without
       // resolving any other types or dependencies out of these containers.
       if (is_null($constructor)) {
           array_pop($this->buildStack);

           return new $concrete;
       }

       $dependencies = $constructor->getParameters();

       // Once we have all the constructor's parameters we can create each of the
       // dependency instances and then use the reflection instances to make a
       // new instance of this class, injecting the created dependencies in.
       $instances = $this->resolveDependencies(
           $dependencies
       );

       array_pop($this->buildStack);

       return $reflector->newInstanceArgs($instances);
   }


   /**
    * Get the last parameter override.
    *
    * @return array
    */
   protected function getLastParameterOverride()
   {
       return count($this->with) ? end($this->with) : [];
   }


   /**
    * Throw an exception that the concrete is not instantiable.
    *
    * @param  string  $concrete
    * @param  string  $cause
    * @return void
    *
    * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
   protected function notInstantiable($concrete, string $cause = '')
   {
       if (! empty($this->buildStack)) {
           $previous = implode(', ', $this->buildStack);

           $message = "Target [$concrete] is not instantiable while building [$previous].";
       } else {
           $message = "Target [$concrete] is not instantiable.";
       }

       throw new BindingResolutionException("$message: $cause");
   }


   /**
    * Resolve all of the dependencies from the ReflectionParameters.
    *
    * @param  ReflectionParameter[]  $dependencies
    * @return array
    *
    * @throws BindingResolutionException
    */
   protected function resolveDependencies(array $dependencies) : array
   {
       $results = [];

       foreach ($dependencies as $dependency) {
           // If this dependency has a override for this particular build we will use
           // that instead as the value. Otherwise, we will continue with this run
           // of resolutions and let reflection attempt to determine the result.
           if ($this->hasParameterOverride($dependency)) {
               $results[] = $this->getParameterOverride($dependency);

               continue;
           }

           if ($this->hasParameterTypeOverride($dependency)) {
               $results[] = $this->getParameterTypeOverride($dependency);

               continue;
           }

           // If the class is null, it means the dependency is a string or some other
           // primitive type which we can not resolve since it is not a class and
           // we will just bomb out with an error since we have no-where to go.
           $results[] = is_null($dependency->getClass())
               ? $this->resolvePrimitive($dependency)
               : $this->resolveClass($dependency);
       }

       return $results;
   }


   /**
    * Determine if the given dependency has a parameter override.
    *
    * @param  \ReflectionParameter  $dependency
    * @return bool
    */
   protected function hasParameterOverride(ReflectionParameter $dependency) : bool
   {
       return array_key_exists(
           $dependency->name, $this->getLastParameterOverride()
       );
   }

    /**
     * 判断参数定义的类型是否存在
     * 这个类型理论上只能是类名
     * 从而实现临时注入依赖.
     *
     * @param ReflectionParameter $dependency
     * @return bool
     */
    protected function hasParameterTypeOverride(ReflectionParameter $dependency) : bool
    {
        // 参数需要有类型
        return $dependency->hasType()
            // 内置的类型不做匹配, 因为重叠几率太高.
            && !$dependency->getType()->isBuiltin()
            // 类型必须存在. 这里假设, 一种类型的依赖只出现一次而不是多次.
            && array_key_exists(
                $dependency->getType()->getName(),
                $this->getLastParameterOverride()
            );
    }


   /**
    * Get a parameter override for a dependency.
    *
    * @param  \ReflectionParameter  $dependency
    * @return mixed
    */
   protected function getParameterOverride(ReflectionParameter $dependency)
   {
       return $this->getLastParameterOverride()[$dependency->name];
   }

    /**
     * 允许添加类型参数, 来实现按类型的临时依赖注入
     *
     * @param ReflectionParameter $dependency
     * @return mixed
     */
   protected function getParameterTypeOverride(ReflectionParameter $dependency)
   {
        return $this->getLastParameterOverride()[$dependency->getType()->getName()];
   }


   /**
    * Resolve a non-class hinted primitive dependency.
    *
    * @param  \ReflectionParameter  $parameter
    * @return mixed
    *
    * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
   protected function resolvePrimitive(ReflectionParameter $parameter)
   {
       if ($parameter->isDefaultValueAvailable()) {
           return $parameter->getDefaultValue();
       }

       $this->unresolvablePrimitive($parameter);
       return null;
   }


   /**
    * Throw an exception for an unresolvable primitive.
    *
    * @param  \ReflectionParameter  $parameter
    * @return void
    *
    * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
   protected function unresolvablePrimitive(ReflectionParameter $parameter) : void
   {
       $message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";

       throw new BindingResolutionException($message);
   }

   /**
    * Resolve a class based dependency from the container.
    *
    * @param  \ReflectionParameter  $parameter
    * @return mixed
    *
    * @throws BindingResolutionException
    */
   protected function resolveClass(ReflectionParameter $parameter)
   {
       try {
           return $this->make($parameter->getClass()->name);
       }

           // If we can not resolve the class instance, we will check to see if the value
           // is optional, and if it is we will return the optional parameter value as
           // the value of the dependency, similarly to how we do this with scalars.
       catch (BindingResolutionException $e) {
           if ($parameter->isOptional()) {
               return $parameter->getDefaultValue();
           }

           throw $e;
       }
   }


   /**
    * Get the concrete type for a given abstract.
    *
    * @param  string  $abstract
    * @return mixed   $concrete
    */
   protected function getConcrete(string $abstract)
   {
       // 所有上下文都不用.
       // if (! is_null($concrete = $this->getContextualConcrete($abstract))) {
       //     return $concrete;
       // }

       // If we don't have a registered resolver or concrete for the type, we'll just
       // assume each type is a concrete name and will attempt to resolve it as is
       // since the container should be able to resolve concretes automatically.
       if (isset(self::$bindings[$abstract])) {
           return self::$bindings[$abstract]['concrete'];
       }

       return $abstract;
   }


   /**
    * Determine if the given concrete is buildable.
    *
    * @param  mixed   $concrete
    * @param  string  $abstract
    * @return bool
    */
   protected function isBuildable($concrete, string $abstract) : bool
   {
       return $concrete === $abstract || $concrete instanceof Closure;
   }


   /**
    * Determine if a given type is shared.
    *
    * @param  string  $abstract
    * @return bool
    */
   public function isShared(string $abstract) : bool
   {
       return isset(self::$instances[$abstract]) ||
           (isset(self::$bindings[$abstract]['shared']) &&
               self::$bindings[$abstract]['shared'] === true);
   }


   /**
    * "Extend" an abstract type in the container.
    *
    * @param  string    $abstract
    * @param  \Closure  $closure
    * @return void
    *
    * @throws \InvalidArgumentException
    */
   public function extend(string $abstract, Closure $closure) : void
   {
       $abstract = $this->getAlias($abstract);

       if (isset(self::$instances[$abstract])) {
           self::$instances[$abstract] = $closure(self::$instances[$abstract], $this);

           // 都暂时不用事件.
           // $this->rebound($abstract);

       } else {
           self::$extenders[$abstract][] = $closure;

           // 暂时不用事件.
           // if ($this->resolved($abstract)) {
           //     $this->rebound($abstract);
           // }
       }
   }



   /**
    * Get the extender callbacks for a given type.
    *
    * @param  string  $abstract
    * @return Closure[]
    */
   protected function getExtenders($abstract)
   {
       $abstract = $this->getAlias($abstract);

       if (isset(self::$extenders[$abstract])) {
           return self::$extenders[$abstract];
       }

       return [];
   }

   /**
    * Determine if a given offset exists.
    *
    * @param  string  $key
    * @return bool
    */
   public function offsetExists($key)
   {
       return $this->bound($key);
   }

   /**
    * Get the value at a given offset.
    *
    * @param  string  $key
    * @return mixed
    * @throws BindingResolutionException
    * @throws \ReflectionException
    */
   public function offsetGet($key)
   {
       return $this->make($key);
   }

   /**
    * Set the value at a given offset.
    *
    * @param  string  $key
    * @param  mixed   $value
    * @return void
    */
   public function offsetSet($key, $value)
   {
       $this->bind($key, $value instanceof Closure ? $value : function () use ($value) {
           return $value;
       });
   }

   /**
    * Unset the value at a given offset.
    *
    * @param  string  $key
    * @return void
    */
   public function offsetUnset($key)
   {
       $this->dropStaleInstances($key);
       unset(self::$bindings[$key]);
   }


   /**
    * Dynamically access container services.
    *
    * @param  string  $key
    * @return mixed
    */
   public function __get($key)
   {
       return $this[$key];
   }

   /**
    * Dynamically set container services.
    *
    * @param  string  $key
    * @param  mixed   $value
    * @return void
    */
   public function __set($key, $value)
   {
       $this[$key] = $value;
   }
}