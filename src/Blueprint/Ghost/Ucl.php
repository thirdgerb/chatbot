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

use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\StringUtils;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Exceptions\InvalidQueryException;
use PharIo\Manifest\InvalidUrlException;

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
     * @var Context|null
     */
    protected $context;

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
        $this->_query = $query;
    }

    public static function make(
        string $contextName,
        array $query = []
    ): Ucl
    {
        $contextName = ContextUtils::normalizeContextName($contextName);
        return new static($contextName, '', $query);
    }


    public static function create(Cloner $cloner, string $contextName, array $query = null, string $stageName = '')  : Ucl
    {
        $contextName = ContextUtils::normalizeContextName($contextName);
        $stageName = ContextUtils::normalizeStageName($stageName);

        $ucl = new static($contextName, $stageName, $query);
        return $ucl->toInstance($cloner);
    }


    public function getContextId() : string
    {
        return $this->contextId
            ?? $this->contextId == sha1(json_encode([
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

    public function equals(string $ucl) : bool
    {
        $decoded = Ucl::decodeUclStr($ucl);
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
            throw new InvalidArgumentException("invalid stage pattern of $stageName");
        }

        /**
         * @var Ucl $ucl
         */
        $ucl = new static($this->_contextName, $stageName, $this->_query);
        $ucl->contextDef = $this->contextDef;
        $ucl->contextId = $this->contextId;

        return $ucl;
    }


    public function goStageByIntentName(string $intentName) : Ucl
    {
        if (!ContextUtils::isValidStageFullName($intentName)) {
            throw new InvalidArgumentException("invalid stage fullname pattern of $intentName");
        }

        $stageName = str_replace($this->_contextName, '', $intentName);
        $stageName = trim($stageName, Context::NAMESPACE_SEPARATOR);
        return $this->goStage($stageName);
    }

    /*------- create -------*/

    /**
     * @param string|Ucl $string
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public static function decodeUclStr($string) : Ucl
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

        return new self($contextName, $stageName, $query);
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



    public function toEncodedStr() : string
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

    public function getStageIntentName(string $stage = null) : string
    {
        return $this->getStageFullname($stage);
    }

    public function getStageFullname(string $stage = null) : string
    {
        $stage = $stage ?? $this->_stageName;
        return StringUtils::gluePrefixAndName(
            $this->_contextName,
            $stage,
            Context::CONTEXT_STAGE_SEPARATOR
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

        $params = $contextDef->getQueryParams();
        $values = $params->parseValues($this->_query);
        $values = array_map(function($value) {
            if (is_null($value)) {
                throw new InvalidQueryException($this->_contextName);
            }
            return $value;
        }, $values);

        $scopes = $contextDef->getScopes();
        $map = $cloner->scope->getLongTermDimensionsDict($scopes);

        $query = $values + $map;

        $instance = new static($this->_contextName, $this->_stageName, $query);
        $instance->instanced = true;

        return $instance;
    }


    /*------- find -------*/

    public function findIntentDef(Cloner $cloner) : ? IntentDef
    {
        $intentName = $this->getStageIntentName();

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
        if (isset($this->context)) {
            return $this->context;
        }

        if (!$this->instanced) {
            $ucl = $this->toInstance($cloner);
            return $ucl->findContext($cloner);
        }

        $def = $this->findContextDef($cloner);
        $context = $def->wrapContext($cloner, $this);

        // 与 entity 合并
        $manager = $def->getEntityParams();
        if ($manager->countParams()) {
            $entities = $cloner->input
                ->comprehension
                ->intention
                ->getIntentEntities($def->getName());

            $entities = $manager->parseValues($entities);
            $context->merge($entities);
        }

        return $this->context = $context;
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


    /*------- magic -------*/

    public function __isset($name)
    {
        return in_array($name, ['contextName', 'stageName', 'query']);
    }

    public function __get($name)
    {
        $name = "_$name";
        return property_exists($this, $name)
            ? $this->{$name}
            : null;
    }

    public function __toString() : string
    {
        return $this->toEncodedStr();
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
        $this->stageDef = null;
        $this->intentDef = null;
        $this->contextDef = null;
        $this->context = null;
    }
}