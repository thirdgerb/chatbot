<?php


namespace Commune\Chatbot\OOHost\Context;


use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Chatbot\Framework\Messages\AbsMessage;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Helpers\ScalarParser;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;
use Commune\Chatbot\OOHost\Session\SessionInstance;

/**
 * Context 的公共抽象. 具体实现有三类:
 *
 * - OOContext : 面向对象的标准 context
 * - Intent : 意图描述. 可以对用户意图进行匹配.
 * - Memory : 记忆, 可以用于上下文存储数据.
 */
abstract class AbsContext extends AbsMessage implements Context
{
    const GETTER_PREFIX = '__get';
    const SETTER_PREFIX = '__set';

    const CAST_TYPE_STRING = 'string';
    const CAST_TYPE_STRING_LIST = 'string[]';
    const CAST_TYPE_INT = 'int';
    const CAST_TYPE_INT_LIST = 'int[]';
    const CAST_TYPE_FLOAT = 'float';
    const CAST_TYPE_FLOAT_LIST = 'float[]';
    const CAST_TYPE_BOOL = 'bool';
    const CAST_TYPE_BOOL_LIST = 'bool[]';

    const GC_COUNT_FIELD = '__gc_count';
    /**
     * 需要进行类型转换的属性.
     */
    const CASTS = [
        // 'property name' => 'float[]' ## defined type from CASTS_TYPES, such as float[]
    ];

    /**
     * @var Session
     */
    protected $_session;

    /**
     * 在传入session 之前, 持有构造参数的临时数组.
     * 只有调用了 toInstance() 之后,
     * 才会用 __set 将这些参数给context 赋值.
     *
     * 属性之所以用 '_' 做前缀, 是防止 __get 方法定义了重复的名称, 导致内部逻辑错误.
     * @var array
     */
    public $_props = [];

    /**
     * context 真正持有属性数据的地方.
     * @var array
     */
    protected $_attributes = [];

    /**
     * contextId
     * 在session 存储中获取context 的ID
     *
     * @var string
     */
    protected $_contextId;

    /**
     * 数据是否发生变动, 决定session 是否要存储.
     * @var bool
     */
    protected $_changed = true;


    /**
     * AbsContext constructor.
     * @param array $props
     */
    public function __construct(array $props = [])
    {
        $this->fillProperties($props);
        parent::__construct();
    }

    /**
     * 一般的 context 序列化只保存这两个数据.
     * memory 需要额外保存 name
     *
     * @return array
     */
    public function __sleep(): array
    {
        $this->getId();
        return array_merge(parent::__sleep(), [
            '_contextId',
            '_attributes',
        ]);
    }

    public function toMessageData(): array
    {
        return [
            'contextId' => $this->getId(),
            'contextName' => $this->getName(),
            'attributes' => $this->toAttributes(),
        ];
    }

    public function toAttributes() : array
    {
        return ArrayUtils::recursiveToArray($this->_attributes);
    }


    /*------- construct -------*/

    public function fillProperties(array $props)
    {
        if (!$this->isInstanced()) {
            $this->_props = $props + $this->_props;
        } else {
            foreach ($props as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /*------- methods -------*/


    public function isInstanced(): bool
    {
        return isset($this->_session);
    }

    public function nameEquals(string $name): bool
    {
        $name = StringUtils::namespaceSlashToDot($name);
        return $name == $this->getName();
    }


    public function getSession(): Session
    {
        return $this->_session;
    }



    public function getCasts() : array
    {
        return static::CASTS;
    }

    /**
     * 赋值的时候进行类型转换.
     *
     * 意图匹配时很可能获取一个或者多个同类型实体. 每个实体都是字符串.
     * 而实际需求的值是数组或需要转换的 scalar type.
     * 所以在赋值时做检查, 在取值时做过滤.
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function cast(string $name, $value)
    {
        $casts = $this->getCasts();
        if (array_key_exists($name, $casts)) {

            $type = $casts[$name] ?? null;

            if (empty($type)) {
                return $value;
            }

            switch($type) {
                case self::CAST_TYPE_STRING :
                    return ScalarParser::toString($value);
                case self::CAST_TYPE_STRING_LIST :
                    return ScalarParser::toStringArr($value);
                case self::CAST_TYPE_INT :
                    return ScalarParser::toInt($value);
                case self::CAST_TYPE_INT_LIST :
                    return ScalarParser::toIntArr($value);
                case self::CAST_TYPE_FLOAT :
                    return ScalarParser::toFloat($value);
                case self::CAST_TYPE_FLOAT_LIST :
                    return ScalarParser::toFloatArr($value);
                case self::CAST_TYPE_BOOL :
                    return ScalarParser::toBool($value);
                case self::CAST_TYPE_BOOL_LIST :
                    return ScalarParser::toBoolArr($value);
                default :
                    $this->getSession()->logger->error(
                        static::class
                        . " try to cast property $name to scalar value failed, type $type given"
                    );
                    return null;
            }
        }
        return $value;
    }

    protected function checkCast(string $name, $value) : void
    {
        if (array_key_exists($name, static::CASTS)) {
            $this->scalarCheck($name, $value);
        }
    }

    public function setAttribute(string $name, $value) : void
    {
        $this->hasInstanced();

        // 目标数据初始化.
        if ($value instanceof SessionInstance) {
            $value = $value->toInstance($this->_session);
        }

        // 执行 gc_check
        if ($value instanceof Context) {
            $this->_gc_check($name, $value);
        }

        // 缓存到 session
        if ($value instanceof SessionData) {
            $this->_session->repo->cacheSessionData($value);
            // 持有 identity
            $value = $value->toSessionIdentity();
        }

        // 检查赋值的类型是否正确.
        $this->checkCast($name, $value);

        $this->_changed = true;

        $this->_attributes[$name] = $value;
    }

    public function getAttribute(string $name)
    {
        $this->isInstanced();
        $value = $this->_attributes[$name] ?? null;

        if (is_null($value)) {
            return null;
        }

        if ($value instanceof SessionDataIdentity) {
            $value = $this->_session->repo->fetchSessionData($this->_session, $value);
        }

        if ($value instanceof SessionInstance) {
            $value = $value->toInstance($this->_session);
        }

        return $this->cast($name, $value);
    }

    /**
     * 赋值时做 context 计数器的检查.
     *
     * @param string $name
     * @param Context|null $value
     */
    protected function _gc_check(string $name, Context $value = null)
    {
        $before = $this->__get($name);
        if (isset($before) && $before instanceof Context) {
            $before->_gc_decrement_count();
        }

        if (isset($value)) {
            $value->_gc_increment_count();
        }
    }

    protected function scalarCheck(string $name, $value) : void
    {
        if (is_array($value)) {
            foreach ($value as $i) {
                if (! is_scalar($i)) {
                    $this->scalarWarning($name, $value);
                    return;
                }
            }

        } else {
            if (! is_scalar($value)) {
                $this->scalarWarning($name, $value);
            }
        }
    }

    protected function scalarWarning(string $name, $value) : void
    {
        if (!$this->isInstanced()) {
            return;
        }
        $type = is_object($value) ? get_class($value) : gettype($value);
        $this->getSession()->logger->warning(
            static::class
            . " try to set property $name with non scalar value type $type"
        );
    }

    public function hasAttribute(string $name) : bool
    {
        return isset($this->_attributes[$name]);
    }

    /**
     * context 许多操作必须要有session参与
     * 这类操作会检查session 是否存在.
     */
    protected function hasInstanced()  : void
    {
        if (!isset($this->_session)) {
            throw new ChatbotLogicException(
                __METHOD__
                . ' can not be called before ->toInstance(session)'
            );
        }
    }

    public function __get(string $name)
    {
        if (!$this->isInstanced()) {
            return $this->_props[$name] ?? null;
        }

        // 检查 getter 方法是否存在
        $method = static::GETTER_PREFIX . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        // 检查是不是走 entity 的逻辑.
        if ($this->getDef()->hasEntity($name)) {
            return $this->getDef()->getEntity($name)->get($this);
        }

        // 获取数据.
        return $this->getAttribute($name);
    }

    public function __set(string $name, $value): void
    {
        if (!$this->isInstanced()) {
            $this->_props[$name] = $value;
            return;
        }

        $method = static::SETTER_PREFIX . ucfirst($name);

        if (method_exists($this, $method)) {
            $this->{$method}($value);
            return;
        }

         // entity
        $def = $this->getDef();
        if ($def->hasEntity($name)) {
            $def->getEntity($name)->set($this, $value);

        } else {
            $this->setAttribute($name, $value);
        }

    }

    public function __unset(string $name): void
    {
        $this->hasInstanced();
        $def = $this->getDef();
        if ($def->hasEntity($name)) {
            $def->getEntity($name)->set($this, null);

        } else {
            $this->setAttribute($name, null);
        }
    }

    public function __isset(string $name): bool
    {
        $this->hasInstanced();

        $def = $this->getDef();
        if ($def->hasEntity($name)) {
            return $def->getEntity($name)->isPrepared($this);

        } else {
            return isset($this->_attributes[$name]);
        }
    }


    /*-------- array access --------*/

    public function offsetExists($offset)
    {
        return $this->__isset((string)$offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get((string)$offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->__set((string)$offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->__unset((string)$offset);
    }

    /*------- definition -------*/


    /*------- contextual -------*/

    public function isPrepared(): bool
    {
        return is_null($this->depending());
    }

    /**
     * @return Entity[]
     */
    public function depends() : array
    {
        return $this->getDef()->dependsEntities($this);
    }

    public function depending(): ? Entity
    {
        return $this->getDef()->dependingEntity($this);
    }


    /*------- message -------*/

    public function isEmpty(): bool
    {
        $names = $this->getDef()->getEntityNames();

        if (empty($names)) return false;

        foreach($names as $name) {
            if ($this->__isset($name)) {
                return false;
            }
        };
        return true;
    }

    public function getText(): string
    {
        return $this->toJson();
    }

    protected function assign()
    {
        if (!empty($this->_props)) {
            foreach ($this->_props as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }


    /**
     * 反序列化的context 不用急于保存.
     */
    public function __wakeup(): void
    {
        $this->_changed = false;
    }

    /**
     * 对于 clone 操作, 通常关键的就是contextId
     */
    public function __clone()
    {
        unset($this->_contextId);
    }

    /*------- session data -------*/

    public function toSessionIdentity(): SessionDataIdentity
    {
        return new SessionDataIdentity(
            $this->getId(),
            $this->getSessionDataType()
        );
    }

    public function shouldSave(): bool
    {
        return $this->_changed;
    }

    public function getSessionDataType(): string
    {
        return SessionData::CONTEXT_TYPE;
    }

    public function getSessionDataId(): string
    {
        return $this->getId();
    }

    /*----------- 极简的 gc 实现, 用于清除绝大部分一次性的 context 缓存 -----------*/

    final public function _gc_increment_count(): void
    {
        $this->getSession()->repo->incrGcCount($this);
    }

    final public function _gc_decrement_count(): void
    {
        $this->getSession()->repo->decrGcCount($this);
    }

    final public function _gc_count(): int
    {
        return $this->getSession()->repo->getGcCount($this);
    }


}