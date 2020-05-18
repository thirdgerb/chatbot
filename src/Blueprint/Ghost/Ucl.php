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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Utils\StringUtils;

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

    const STAGE_SEPARATOR = '/';
    const QUERY_SEPARATOR = '?';

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $stageName;

    /**
     * @var string[]
     */
    protected $query;

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
        $this->contextName = $contextName;
        // 真正的 contextName 和 stageName 必须全小写, 用 . 分割.
        $this->stageName = $stageName;
        $this->query = $query;
    }


    public static function create(Cloner $cloner, string $contextName, array $query = null, string $stageName = '')  : Ucl
    {
        $contextName = ContextUtils::normalizeContextName($contextName);
        $stageName = ContextUtils::normalizeStageName($stageName);
        $query = $cloner->getContextualQuery($contextName, $query);
        return new self($contextName, $stageName, $query);
    }

    public static function createFromUcl(
        Cloner $cloner,
        string $ucl
    ): Ucl
    {
        $ucl = static::decodeUcl($ucl);
        $contextName = $ucl->contextName;
        $stageName = $ucl->stageName;
        $query = $ucl->query;
        return static::create($cloner, $contextName, $stageName, $query);
    }


    /*------- compare -------*/

    public function atSameContext(string $ucl) : bool
    {
        return strpos($ucl, $this->contextName) === 0;
    }

    public function isSameContext(string $ucl): bool
    {
        return $this->getContextId() === Ucl::decodeUcl($ucl)->getContextId();
    }


    public function equals(string $ucl)
    {
        $decoded = Ucl::decodeUcl($ucl);
        return $this->getContextId() === $decoded->getContextId()
            && $this->stageName === $decoded->stageName;
    }


    /*------- redirect -------*/

    public function goStage(string $stageName) : Ucl
    {
        if (!ContextUtils::isValidStageName($stageName)) {
            throw new InvalidArgumentException(
                __METHOD__,
                'stageName',
                "invalid stage pattern of $stageName"
            );
        }
        return new self($this->contextName, $stageName, $this->query);
    }


    public function goFullnameStage(string $fullStageName) : Ucl
    {
        if (!ContextUtils::isValidStageFullName($fullStageName)) {
            throw new InvalidArgumentException(
                __METHOD__,
                'fullStageName',
                "invalid stage fullname pattern of $fullStageName"
            );
        }

        $stageName = str_replace($this->contextName, '', $fullStageName);
        $stageName = trim($stageName, Context::NAMESPACE_SEPARATOR);
        return $this->goStage($stageName);
    }

    /*------- create -------*/

    public function isValid() : bool
    {
        return ContextUtils::isValidContextName($this->contextName)
            && ContextUtils::isValidStageName($this->stageName)
            && is_array($this->query);
    }

    /**
     * @param string|Ucl $string
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public static function decodeUcl($string) : Ucl
    {
        if ($string instanceof Ucl) {
            return $string;
        }

        if (!is_string($string)) {
            throw new InvalidArgumentException(
                __METHOD__,
                'string',
                'should be Ucl instance or Ucl string'
            );
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



    public function toEncodedUcl() : string
    {
        if (isset($this->encoded)) {
            return $this->encoded;
        }

        return $this->encoded
            ?? $this->encoded = self::encodeUcl(
                $this->contextName,
                $this->stageName,
                $this->query
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

    public function toIntentName(string $stage = null) : string
    {
        return $this->toFullStageName($stage);
    }


    public function toFullStageName(string $stage = null) : string
    {
        $stage = $stage ?? $this->stageName;
        return StringUtils::gluePrefixAndName(
            $this->contextName,
            $stage,
            Context::NAMESPACE_SEPARATOR
        );
    }


    public function getContextId() : string
    {
        return $this->contextId
            ?? $this->contextId == sha1(json_encode([
               'contextName' => $this->contextName,
               'query' => $this->query,
            ]));
    }


    /*------- def -------*/

    public function findIntentDef(Cloner $cloner) : ? IntentDef
    {
        $intentName = $this->toIntentName();

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
            ?? $this->exists = $this->isValid()
                && $cloner->mind->stageReg()->hasDef($this->toFullStageName());
    }

    public function findStageDef(Cloner $cloner) : StageDef
    {
        return $this->stageDef
            ?? $this->stageDef = $cloner
                ->mind
                ->stageReg()
                ->getDef($this->toFullStageName());
    }

    public function findContextDef(Cloner $cloner) : ContextDef
    {
        return $this->contextDef
            ?? $this->contextDef = $cloner
                ->mind
                ->contextReg()
                ->getDef($this->contextName);
    }

    public function __isset($name)
    {
        return in_array($name, ['contextName', 'stageName', 'query']);
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function __toString() : string
    {
        return $this->toEncodedUcl();
    }

    public function __sleep()
    {
        return [
            'contextName',
            'stageName',
            'query'
        ];
    }

    public function __destruct()
    {
        $this->stageDef = null;
        $this->intentDef = null;
        $this->contextDef = null;
    }
}