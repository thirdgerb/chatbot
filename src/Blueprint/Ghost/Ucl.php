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
use Commune\Framework\Spy\SpyAgency;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\StringUtils;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Exceptions\InvalidQueryException;

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
     * @var bool
     */
    protected $instanced = false;

    /*---- cached ---*/

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

    public static function make(
        string $contextName,
        array $query = [],
        string $stageName = ''
    ): Ucl
    {
        $contextName = ContextUtils::normalizeContextName($contextName);
        return new static($contextName, $stageName, $query);
    }


    public static function newInstance(Cloner $cloner, string $contextName, array $query = null, string $stageName = '')  : Ucl
    {
        $contextName = ContextUtils::normalizeContextName($contextName);
        $stageName = ContextUtils::normalizeStageName($stageName);

        $ucl = new static($contextName, $stageName, $query);
        return $ucl->toInstance($cloner);
    }

    public static function parseIntentName($ucl): ? string
    {
        if ($ucl instanceof Ucl) {
            return $ucl->getStageFullname();
        }

        $parts = explode(self::QUERY_SEPARATOR, $ucl, 2);
        $first = $parts[0] ?? '';

        // 生成合法的 intentName
        $intentName = str_replace(
            self::STAGE_SEPARATOR,
            Context::CONTEXT_STAGE_DELIMITER,
            $first
        );

        return ContextUtils::isValidIntentName($intentName)
            ? $intentName
            : null;
    }


    public function getContextId() : string
    {
        return $this->contextId
            ?? $this->contextId = sha1(json_encode([
                'contextName' => $this->_contextName,
                'query' => $this->_query,
            ]));
    }

    /*------- compare -------*/

    public function atSameContext(Ucl $ucl) : bool
    {
        return $this->_contextName === $ucl->contextName;
    }

    /**
     * @param Ucl $ucl
     * @return bool
     */
    public function isSameContext(Ucl $ucl): bool
    {
        return $this->atSameContext($ucl)
            && $this->getContextId() === $ucl->getContextId();
    }

    public function equals($ucl) : bool
    {
        $decoded = Ucl::decode($ucl);
        return $this->getContextId() === $decoded->getContextId()
            && $this->_stageName === $decoded->stageName;
    }

    public function isInstanced(): bool
    {
        return $this->instanced;
    }

    public function isValid(Cloner $cloner): bool
    {
        return $this->isInstanced()
            && $this->isValidPattern()
            && $this->stageExists($cloner);
    }

    public function isValidPattern() : bool
    {
        return ContextUtils::isValidContextName($this->_contextName)
            && ContextUtils::isValidStageName($this->_stageName)
            && is_array($this->_query);
    }


    /*------- redirect -------*/

    public function goStage(string $stageName = ContextDef::START_STAGE_NAME) : Ucl
    {
        if (!ContextUtils::isValidStageName($stageName)) {
            throw new InvalidArgumentException("invalid stage pattern: $stageName");
        }

        /**
         * @var Ucl $ucl
         */
        return new static($this->_contextName, $stageName, $this->_query);
    }


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
            throw new InvalidArgumentException('should be Ucl instance or Ucl string');
        }

        $string = strval($string);
        $ex = explode(Ucl::QUERY_SEPARATOR, $string, 2);
        $prefix = $ex[0];
        $queryStr = $ex[1] ?? '';

        $query = static::decodeQueryStr($queryStr);
        $ex = explode(Ucl::STAGE_SEPARATOR, $prefix, 2);

        $contextName = $ex[0];
        $stageName = $ex[1] ?? '';
        $query = is_array($query) ? $query : [];

        return static::make($contextName, $query, $stageName);
    }

    public static function decodeQueryStr(string $str) : array
    {
        if (empty($str)) {
            return [];
        }

        $query = [];
        parse_str($str, $query);
        return $query;
    }



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

    public function toString(): string
    {
        return $this->encode();
    }

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

    public static function encodeQueryStr(array $query) : string
    {
        return http_build_query($query);
    }

    /*------- property -------*/

    public function getStageFullname(string $stage = null) : string
    {
        $stage = $stage ?? $this->_stageName;
        return StringUtils::gluePrefixAndName(
            $this->_contextName,
            $stage,
            Context::CONTEXT_STAGE_DELIMITER
        );
    }

    /*------- instance -------*/

    public function toInstance(Cloner $cloner): Ucl
    {
        if (!$this->stageExists($cloner)) {
            throw new DefNotDefinedException(
                StageDef::class,
                $this->getStageFullname()
            );
        }

        if ($this->instanced) {
            return $this;
        }

        $contextDef = $this->findContextDef($cloner);
        $queryNames = $contextDef->getQueryNames();
        $query = $this->mergeQuery($queryNames, $contextDef, $cloner);

        $instance = new static($this->_contextName, $this->_stageName, $query);
        $instance->instanced = true;

        return $instance;
    }

    protected function mergeQuery(array $queryNames, ContextDef $contextDef, Cloner $cloner) : array
    {
        $query = empty($queryNames)
            ? []
            : ArrayUtils::parseValuesByKeysWithListMark(
                $this->_query,
                $queryNames
            );

        $scopes = $contextDef->getScopes();
        $map = $cloner->scope->getLongTermDimensionsDict($scopes);

        // query 定义值的优先级高于当前作用域的值.
        $query = $query + $map;

        // query 值不能为 null.
        array_walk($query, function ($value, $index) {
            if (is_null($value)) {
                throw new InvalidQueryException(
                    $this->contextName,
                    $index,
                    'is required'
                );
            }
        });

        return $query;
    }


    /*------- find -------*/

    public function findIntentDef(Cloner $cloner) : ? IntentDef
    {
        $intentName = $this->getStageFullname();

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

    public function stageExists(Cloner $cloner) : bool
    {
        return $this->exists
            ?? $this->exists = $cloner->mind->stageReg()->hasDef($this->getStageFullname());
    }

    public function findStageDef(Cloner $cloner) : StageDef
    {
        return $this->stageDef
            ?? $this->stageDef = $cloner
                ->mind
                ->stageReg()
                ->getDef($this->getStageFullname());
    }

    public function findContextDef(Cloner $cloner) : ContextDef
    {
        return $this->contextDef
            ?? $this->contextDef = $cloner
                ->mind
                ->contextReg()
                ->getDef($this->_contextName);
    }

    public function findContext(Cloner $cloner): Context
    {
        if (!$this->instanced) {
            $ucl = $this->toInstance($cloner);
            return $ucl->findContext($cloner);
        }

        $runtime = $cloner->runtime;
        $context = $runtime->getCachedContext($this->getContextId());

        if (isset($context)) {
            return $context;
        }

        $def = $this->findContextDef($cloner);
        $context = $def->wrapContext($cloner, $this);

        // 是否意图匹配中命中了 entities
        $entities = $cloner->input
            ->comprehension
            ->intention
            ->getIntentEntities($this->getStageFullname());

        // 匹配到了 entity 的话, 默认会合并.
        if (!empty($entities)) {
            $intentDef = $this->findIntentDef($cloner);
            $entities = $intentDef->parseEntities($entities);
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

    /*------- dependable -------*/

    public function toInstanceStub(): ClonerInstanceStub
    {
        return $this;
    }

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
            'instanced',
        ];
    }

    public function __destruct()
    {
        unset($this->stageDef);
        unset($this->intentDef);
        unset($this->contextDef);
        SpyAgency::decr(static::class);
    }

}