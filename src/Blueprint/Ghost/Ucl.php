<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Ghost\Cloner\ClonerInstanceStub;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\Exceptions\InvalidQueryException;
use Commune\Framework\Spy\SpyAgency;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\StringUtils;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Support\Utils\TypeUtils;

/**
 * Uniform Context Locator
 * 类似 url 的语境定位对象. 用一个 ucl 字符串可以定位一个语境.
 *
 * commune.context.test.demo/stage_abc?a=1&b=2
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextName       语境名. 相当于 url 的域名
 * @property-read string $stageName         stage名. 相当于 url 的路径
 * @property-read string[] $query           query参数, 相当于 url 的query
 * @property-read string $queryStr          query参数的字符串.
 *
 *
 * 相关 API 文档:
 * @see UclInterface
 */
class Ucl implements UclInterface
{
    use ArrayAbleToJson;

    const STAGE_SEPARATOR = '/';
    const QUERY_SEPARATOR = '?';

    /**
     * @var string
     */
    protected $_contextName;

    /**
     * @var string
     */
    protected $_stageName;

    /**
     * @var string[]
     */
    protected $_query;

    /**
     * @var string|null
     */
    protected $_asIntent;

    /*---- cached ---*/

    /**
     * @var string|false|null
     */
    protected $invalid;

    /**
     * @var string|null
     */
    protected $encoded;

    /**
     * @var string|null
     */
    protected $contextId;

    /**
     * @var StageDef|null
     */
    protected $stageDef;

    /**
     * @var ContextDef|null
     */
    protected $contextDef;

    /**
     * @var IntentDef|bool
     */
    protected $intentDef;

    /**
     * @var bool|null
     */
    protected $exists;

    /**
     * Ucl constructor.
     * @param string $contextName
     * @param string $stageName
     * @param array $query
     */
    private function __construct(
        string $contextName,
        string $stageName = '',
        array $query = []
    )
    {
        // 允许使用类名作为 contextName
        $this->_contextName = $contextName;
        // 真正的 contextName 和 stageName 必须全小写.
        $this->_stageName = $stageName;
        // 排序一下.
        ksort($query);
        $this->_query = $query;
        SpyAgency::incr(static::class);
    }

    /**
     * 生成一个 Ucl 对象.
     * @param string $contextName
     * @param array $query
     * @param string $stageName
     * @return Ucl
     */
    public static function make(
        string $contextName,
        array $query = [],
        string $stageName = ''
    ): Ucl
    {
        $contextName = ContextUtils::normalizeContextName($contextName);
        $stageName = ContextUtils::normalizeStageName($stageName);
        return new static($contextName, $stageName, $query);
    }

    /**
     * 获取当前 Context 的唯一 Id
     * @return string
     */
    public function getContextId() : string
    {
        return $this->contextId
            ?? $this->contextId = sha1(json_encode([
                'contextName' => $this->_contextName,
                'query' => $this->_query,
            ]));
    }

    /*------- compare -------*/

    /**
     * 判断两个 Ucl 是否在相同的 Context 下.
     * 即便相同, 也不一定在同一个实例.
     *
     * @param Ucl $ucl
     * @return bool
     */
    public function atSameContext(Ucl $ucl) : bool
    {
        return $this->_contextName === $ucl->contextName;
    }

    /**
     * 判断两个 Ucl 是否在相同的 Context 实例下.
     * @param Ucl $ucl
     * @return bool
     */
    public function isSameContext(Ucl $ucl): bool
    {
        return $this->atSameContext($ucl)
            && $this->getContextId() === $ucl->getContextId();
    }

    /**
     * 判断两个 Ucl 是否完全一致
     * @param string $ucl
     * @return bool
     */
    public function equals($ucl) : bool
    {
        $decoded = Ucl::decode($ucl);
        return $this->getContextId() === $decoded->getContextId()
            && $this->_stageName === $decoded->stageName;
    }


    /**
     * 判断当前的 Ucl 是否合法.
     * 需要 实例化 + 合法格式 + 定义的 stage def 存在.
     * @param Cloner $cloner
     * @return bool
     */
    public function isValid(Cloner $cloner): bool
    {
        $invalid = $this->isInvalid($cloner);
        return is_null($invalid);
    }

    public function isInvalid(Cloner $cloner) : ? string
    {
        if ($this->invalid === false) {
            return null;
        } elseif (is_string($this->invalid)) {
            return $this->invalid;
        }

        $encoded = $this->encode();
        if (!$this->isValidPattern()) {
            return $this->invalid = "ucl $encoded pattern is invalid";
        }

        if (!$this->stageExists($cloner)) {
            return $this->invalid = "ucl $encoded stage not defined";
        }

        $def = $this->findContextDef($cloner);
        $names = $def->getQueryNames();
        foreach ($names as $name) {
            $isList = TypeUtils::isListTypeHint($name);
            $key = $isList
                ? TypeUtils::pureListTypeHint($name)
                : $name;

            if (!isset($key, $this->query)) {
                return $this->invalid = "ucl $encoded miss query value of $key";
            }

            if ($isList && !is_array($this->query[$key])) {
                return $this->invalid = "ucl $encoded expect key $key as array";
            }
        }

        $this->invalid = false;
        return null;
    }

    /**
     * 判断当前 Ucl 是否符合预设的字符串格式.
     * @return bool
     */
    public function isValidPattern() : bool
    {
        return ContextUtils::isValidContextName($this->_contextName)
            && ContextUtils::isValidStageName($this->_stageName)
            && is_array($this->_query);
    }


    /*------- redirect -------*/

    /**
     * 当前 Ucl 给出另一个 stage 的 ucl
     * @param string $stageName
     * @return Ucl
     */
    public function goStage(string $stageName = ContextDef::START_STAGE_NAME) : Ucl
    {
        $isWildcardPattern = StringUtils::isWildcardPattern($stageName);

        if (!$isWildcardPattern && !ContextUtils::isValidStageName($stageName)) {
            throw new InvalidArgumentException("invalid stage pattern: $stageName");
        }

        /**
         * @var Ucl $ucl
         */
        return new static($this->_contextName, $stageName, $this->_query);
    }

    /**
     * 使用相同 Context 下 stage 的全名来获得一个新的 Ucl
     * 主要是为了传递 Query 等参数.
     *
     * @param string $fullname
     * @return Ucl
     */
    public function goStageByFullname(string $fullname) : Ucl
    {
        if (!ContextUtils::isValidStageFullName($fullname)) {
            throw new InvalidArgumentException("invalid stage fullname pattern of $fullname");
        }

        $stageName = ContextUtils::parseShortStageName(
            $fullname,
            $this->contextName
        );

        return $this->goStage($stageName);
    }

    /*------- create -------*/

    /**
     * 将一个字符串解码成 Ucl 对象, 如果解码不成功, 抛出异常.
     * @param string|Ucl $string
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public static function decode($string) : Ucl
    {
        if ($string instanceof Ucl) {
            return $string;
        }

        if (!is_string($string)) {
            $type = TypeUtils::getType($string);
            throw new InvalidArgumentException("should be Ucl instance or Ucl string, $type given");
        }

        $string = strval($string);
        // 分离 query
        $ex = explode(Ucl::QUERY_SEPARATOR, $string, 2);
        $prefix = $ex[0];
        $queryStr = $ex[1] ?? '';


        $query = static::decodeQueryStr($queryStr);

        // 分离 context 和 stage
        $ex = explode(Ucl::STAGE_SEPARATOR, $prefix, 2);
        $contextName = $ex[0];
        $stageName = $ex[1] ?? '';
        $query = is_array($query) ? $query : [];

        return static::make($contextName, $query, $stageName);
    }

    /**
     * 将一个字符串反解为 query 数组.
     * @param string $str
     * @return array
     */
    public static function decodeQueryStr(string $str) : array
    {
        if (empty($str)) {
            return [];
        }

        $query = [];
        parse_str($str, $query);
        return $query;
    }

    /**
     * 当前 Ucl 生成 ucl 字符串.
     * @return string
     */
    public function encode() : string
    {
        if (isset($this->encoded)) {
            return $this->encoded;
        }

        return $this->encoded
            ?? $this->encoded = self::encodeUcl(
                $this->_contextName,
                $this->_stageName,
                $this->_query
            );
    }

    /**
     * 将 ucl 的参数合成为 ucl 字符串.
     * @param string $contextName
     * @param string $stageName
     * @param array $query
     * @return string
     */
    public static function encodeUcl(
        string $contextName,
        string $stageName = '',
        array $query = []
    ) : string
    {
        $prefix = StringUtils::gluePrefixAndName(
            $contextName,
            $stageName,
            Ucl::STAGE_SEPARATOR
        );

        $ucl = StringUtils::gluePrefixAndName(
            $prefix,
            empty($query) ? '' : static::encodeQueryStr($query),
            Ucl::QUERY_SEPARATOR
        );

        return $ucl;
    }

    /**
     * 将 query 序列化
     * @param array $query
     * @return string
     */
    public static function encodeQueryStr(array $query) : string
    {
        return http_build_query($query);
    }

    /*------- property -------*/

    /**
     * 为 Ucl 手动设置一个另外的意图名
     * @param string $intentName
     * @return Ucl
     */
    public function asIntent(string $intentName) : self
    {
        $this->_asIntent = $intentName;
        return $this;
    }

    /**
     * 获取意图的名称.
     * @return string
     */
    public function getIntentName() : string
    {
        return $this->_asIntent ?? $this->getStageFullname();
    }

    /**
     * 获取 Stage 的完整名称.
     * @param string|null $stage
     * @return string
     */
    public function getStageFullname(string $stage = null) : string
    {
        $stage = $stage ?? $this->_stageName;
        return StringUtils::gluePrefixAndName(
            $this->_contextName,
            $stage,
            Context::CONTEXT_STAGE_DELIMITER
        );
    }

    /*------- find -------*/

    /**
     * 寻找当前 Ucl 是否定义了意图
     * @param Cloner $cloner
     * @return IntentDef|null
     */
    public function findIntentDef(Cloner $cloner) : ? IntentDef
    {
        $intentName = $this->getIntentName();

        if ($this->intentDef === false) {
            return null;
        }

        if ($this->intentDef instanceof IntentDef) {
            return $this->intentDef;
        }

        $reg = $cloner->mind->intentReg();

        if (!$reg->hasDef($intentName)) {
            $this->intentDef = false;
            return null;
        }


        return $this->intentDef = $cloner
            ->mind
            ->intentReg()
            ->getDef($intentName);

    }

    /**
     * 当前 Ucl 对应的 stage 是否定义过.
     * @param Cloner $cloner
     * @return bool
     */
    public function stageExists(Cloner $cloner) : bool
    {
        return $this->exists
            ?? $this->exists = $cloner->mind->stageReg()->hasDef($this->getStageFullname());
    }

    /**
     * 通过 Ucl 获取 stage def
     * @param Cloner $cloner
     * @return StageDef
     */
    public function findStageDef(Cloner $cloner) : StageDef
    {
        return $this->stageDef
            ?? $this->stageDef = $cloner
                ->mind
                ->stageReg()
                ->getDef($this->getStageFullname());
    }

    /**
     * 通过 Ucl 获取 ContextDef
     * @param Cloner $cloner
     * @return ContextDef
     */
    public function findContextDef(Cloner $cloner) : ContextDef
    {
        return $this->contextDef
            ?? $this->contextDef = $cloner
                ->mind
                ->contextReg()
                ->getDef($this->_contextName);
    }

    /**
     * 通过 Ucl 获取 Context 对象
     * @param Cloner $cloner
     * @return Context
     * @throws InvalidArgumentException
     */
    public function findContext(Cloner $cloner): Context
    {
        $contextId = $this->getContextId();
        $runtime = $cloner->runtime;
        $context = $runtime->getCachedContext($contextId);

        if (isset($context)) {
            return $context;
        }

        $invalid = $this->isInvalid($cloner);
        if (isset($invalid)) {
            throw new InvalidQueryException($invalid);
        }

        // 如果没有缓存, 创建一个新的 context.
        $def = $this->findContextDef($cloner);
        $context = $def->wrapContext($cloner, $this);

        // 是否意图匹配中命中了 entities
        $entities = $cloner
            ->comprehension
            ->intention
            ->getIntentEntities($this->getStageFullname());

        // 匹配到了 entity 的话, 默认会合并.
        if (!empty($entities)) {

            $intentDef = $this->findIntentDef($cloner);
            if (isset($intentDef)) {
                $entities = $intentDef->parseEntities($entities);
            }

            $context->merge($entities);
        }

        $runtime->cacheContext($context);
        return $context;
    }

    /*------- to array -------*/

    public function toArray(): array
    {
        return [
            'contextName' => $this->_contextName,
            'contextId' => $this->getContextId(),
            'stageName' => $this->_stageName,
            'query' => $this->_query
        ];
    }



    /*------- cloner instance -------*/


    /**
     * 判断当前的 Ucl 是否已经在 Cloner 中实例化过了.
     * @return bool
     */
    public function isInstanced(): bool
    {
        return true;
    }

    /**
     * 将一个临时生成的 Ucl 进行实例化.
     * @param Cloner $cloner
     * @return Ucl
     */
    public function toInstance(Cloner $cloner): Ucl
    {
        if (!$this->stageExists($cloner)) {
            throw new DefNotDefinedException(
                StageDef::class,
                $this->getStageFullname()
            );
        }
        return $this;
    }

    /**
     * 获取 ClonerInstanceStub, 也就是 memory 中存储的对象.
     * @return ClonerInstanceStub
     */
    public function toInstanceStub(): ClonerInstanceStub
    {
        return $this;
    }

    /*------- dependable -------*/

    public function isFulfilled(): bool
    {
        return false;
    }

    public function toFulfillUcl(): Ucl
    {
        return $this;
    }


    /*------- magic -------*/

    public function __isset($name)
    {
        return in_array($name, ['contextName', 'stageName', 'query', 'queryStr']);
    }

    public function __get($name)
    {
        if ($name === 'queryStr') {
            return static::encodeQueryStr($this->_query);
        }

        $name = "_$name";
        return property_exists($this, $name)
            ? $this->{$name}
            : null;
    }

    public function __toString() : string
    {
        return $this->encode();
    }

    public function __sleep()
    {
        return [
            '_contextName',
            '_stageName',
            '_query',
            '_asIntent',
            'instanced',
        ];
    }

    public function toString(): string
    {
        return $this->encode();
    }


    public function __destruct()
    {
        unset($this->stageDef);
        unset($this->intentDef);
        unset($this->contextDef);
        SpyAgency::decr(static::class);
    }

}