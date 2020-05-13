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

use Commune\Blueprint\Ghost\Mind\Definitions\ContextDef;
use Commune\Blueprint\Ghost\Mind\Definitions\IntentDef;
use Commune\Blueprint\Ghost\Mind\Definitions\StageDef;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Utils\StringUtils;

/**
 * Uniform Context Locator
 * 类似 url 的语境定位对象. 用一个 ucl 字符串可以定位一个语境.
 *
 *
 * 格式为: contextName#stageName?{query}
 * 正则 @see ContextUtils::isValidUcl()
 * 正确的调用方式是 : Cloner::getUcl(contextName, array $query = null);
 *
 * 其中:
 * contextName + query 决定唯一的 contextId
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read string[] $query
 */
class Ucl
{
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
        $this->contextName = ContextUtils::normalizeContextName($contextName);
        // 真正的 contextName 和 stageName 必须全小写, 用 . 分割.
        $this->stageName = StringUtils::normalizeString($stageName);
        $this->query = $query;
    }


    public static function create(Cloner $cloner, string $contextName, array $query = null, string $stageName = '')  : Ucl
    {
        $query = $cloner->getContextualQuery($contextName, $query);
        return new self($contextName, $stageName, $query);
    }

    /*------- redirect -------*/

    public function gotoStage(string $stageName) : Ucl
    {
        return new self($this->contextName, $stageName, $this->query);
    }

    public function isSameContext(string $fullStageName) : bool
    {
        return strpos($fullStageName, $this->contextName) === 0;
    }

    public function gotoFullnameStage(string $fullStageName) : Ucl
    {
        $stageName = str_replace($this->contextName, '', $fullStageName);
        $stageName = trim($stageName, Context::NAMESPACE_SEPARATOR);
        return $this->gotoStage($stageName);
    }

    /*------- create -------*/

    public static function isValid(string $ucl) : bool
    {
        return ContextUtils::isValidUcl($ucl);
    }

    /**
     * @param string|Ucl $string
     * @return Ucl|null
     */
    public static function decodeUcl($string) : ? Ucl
    {
        if ($string instanceof Ucl) {
            return $string;
        }

        $string = strval($string);
        if (!self::isValid($string)) {
            return null;
        }

        $ex = explode('?', $string, 2);
        $prefix = $ex[0];
        $queryStr = $ex[1] ?? '{}';

        $query = json_decode($queryStr, true);
        $ex = explode('#', $prefix, 2);

        $contextName = $ex[0];
        $stageName = $ex[1] ?? '';
        $query = is_array($query) ? $query : [];

        return new self($contextName, $stageName, $query);
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
            '#'
        );

        $ucl = StringUtils::gluePrefixAndName(
            $prefix,
            empty($query) ? '' : json_encode($query),
            '?'
        );

        return $ucl;
    }

    /*------- property -------*/

    public function parseIntentName(string $stage = null) : string
    {
        return $this->parseFullStageName($stage);
    }


    public function parseFullStageName(string $stage = null) : string
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
        $intentName = $this->parseIntentName();

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

    public function exists(Cloner $cloner) : bool
    {
        return $this->exists
            ?? $this->exists = $cloner
                ->mind
                ->stageReg()
                ->hasDef($this->parseFullStageName());
    }

    public function findStageDef(Cloner $cloner) : StageDef
    {
        return $this->stageDef
            ?? $this->stageDef = $cloner
                ->mind
                ->stageReg()
                ->getDef($this->parseFullStageName());
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