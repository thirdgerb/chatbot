<?php

namespace Commune\Support\Property;

use Commune\Support\Arr\ArrayAbleToJson;

/**
 * Class Reader
 * @package Commune\Support
 *
 * read property from resource of multi types
 *
 * 从不同的数据源读取属性.
 * 可以读取的属性, 请用 property-read 注解来标记.
 * 用这种方式实现对不同数据源的解耦. 但不进行强类型校验.
 * 所有属性都认为可以为null
 *
 *
 * 可以通过注册(register) 不同的parser, 增加读取该数据源的能力.
 */
class PropertyReader implements PropertyReaderInterface
{
    use ArrayAbleToJson;

    // 只读pattern
    const PROPERTY_READ_PATTERN = '/@property-read\s+[\w\\\\|\[\]]+\s+\${0,1}(\w+)/';

    /**
     * 可以缓存的参数.
     * @var array
     */
    const CACHED = [
        //'key' => 'default'
    ];

    const TO_ARRAY = [
        //'key',
    ];

    /**
     * @var string
     */
    protected $originType;

    /**
     * @var mixed
     */
    protected $originData;

    /**
     * @var bool
     */
    private $doHasParser;

    /**
     * @var array
     */
    protected $cachedProperties = [];

    /**
     * @var array
     */
    private static $parsers = [];

    /**
     * @var array
     */
    private static $getters = [];

    /**
     * PropertyReader constructor.
     *
     * 通过cached 允许直接传入值
     *
     * @param string $originType
     * @param mixed $origin
     * @param array $cached
     */
    public function __construct(
        string $originType,
        $origin = null,
        array $cached = []
    )
    {
        $this->originType = $originType;
        $this->originData = $origin;
        $this->cachedProperties = $cached;
        $this->init();
    }

    /**
     * @return string
     */
    public function getOriginType(): string
    {
        return $this->originType;
    }

    /**
     * @return mixed
     */
    public function getOriginData()
    {
        return $this->originData;
    }

    /**
     * 实例自身初始化逻辑, 通过继承来重写.
     */
    protected function init() : void
    {
    }

    /**
     * @inheritdoc
     */
    public static function register(string $id, callable $parser) : void
    {
        self::$parsers[static::class][$id] = $parser;
    }

    /**
     * @inheritdoc
     */
    public static function registerParser(PropertyReaderParser $parser) : void
    {
        self::register($parser->getOriginType(), [$parser, 'getter']);
    }


    /**
     * 判断是否有可用的parser
     *
     * @return bool
     * @throws
     */
    public function hasParser() : bool
    {
        // 只计算一次.
        if (isset($this->doHasParser)) {
            return $this->doHasParser;
        }

        // 判断parser 是否存在.

        return $this->doHasParser =
            // 首先 originType 必须存在.
            !empty($this->originType)
            // 再检查 当前类是否注册过parser
            && $this->doHasParser = array_key_exists(static::class, self::$parsers)
            // 再检查 parser 是否存在.
            && array_key_exists($this->originType, self::$parsers[static::class]);
    }

    /**
     * 与 @ property-read 相对应
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getProperty($name)
    {
        // 如果在cached 中有这个值, 就会默认从中获取数据.
        if (array_key_exists($name, $this->cachedProperties)) {
            return $this->cachedProperties[$name];
        }

        // if parser not register, always use default parser
        if (!$this->hasParser()) {
            // try array key
            if (is_array($this->originData)) {
                $value = $this->originData[$name] ?? null;

            // try object property
            } elseif (is_object($this->originData) && property_exists($this->originData, $name)) {
                $value = $this->originData->{$name};

            } else {
                // failed
                return null;
            }

        } else {
            $parser = self::$parsers[static::class][$this->originType];
            // 通过parser 来获取数据.
            $value = call_user_func($parser, $this->originData, $name);

        }

        // 需要缓存的数据
        if (array_key_exists($name, static::CACHED)) {
            return $this->cachedProperties[$name] = $value ?? static::CACHED[$name];
        }

        return $value;
    }


    /**
     * 递归地数组化.
     *
     * @return array
     */
    public function toArray() : array
    {
        // 确定toArray 用哪些字段.
        $getters = static::TO_ARRAY ? : self::keys();

        $result = [];
        foreach($getters as $getter) {
            $value  = $this->__get($getter);
            if (
                is_object($value)
                && method_exists($value, 'toArray')
            ) {
                $result[$getter] = $value->toArray();
            } else {
                $result[$getter] = $value;
            }
        }
        return $result;
    }

    /**
     * 获取所有已有的getters
     * 只考虑注解中定义过的getter
     *
     * @return array
     * @throws
     */
    public static function keys() : array
    {
        if (array_key_exists(static::class, self::$getters)) {
            return self::$getters[static::class];
        }

        $r = new \ReflectionClass(static::class);

        $properties = [];
        preg_match_all(
            static::PROPERTY_READ_PATTERN,
            $r->getDocComment(),
            $properties,
            PREG_SET_ORDER
        );

        $getters = array_map(function($item){
            return $item[1];
        }, $properties);

        self::$getters[static::class] = $getters;
        return $getters;
    }

    /**
     * 序列化的时候只存原数据
     * @return array
     */
    public function __sleep()
    {
        return ['cachedProperties', 'originType', 'originData'];
    }

    /**
     * 启动的时候要重启init
     */
    public function __wakeup()
    {
        $this->init();
    }

}