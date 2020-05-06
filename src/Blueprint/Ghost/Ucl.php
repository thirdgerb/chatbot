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

use Commune\Blueprint\Ghost\Definition\ContextDef;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Ghost\Support\ContextTypeUtils;
use Commune\Support\Utils\StringUtils;

/**
 * Uniform Context Locator
 * 类似 url 的语境定位对象. 用一个 ucl 字符串可以定位一个语境.
 *
 *
 * 格式为: contextName#stageName?{query}
 * 正则 @see ContextTypeUtils::isValidUcl()
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
     * Ucl constructor.
     * @param string $contextName
     * @param string $stageName
     * @param array $query
     */
    public function __construct(
        string $contextName,
        string $stageName = '',
        array $query = [])
    {

        // 允许使用类名作为 contextName
        $this->contextName = ContextTypeUtils::normalizeContextName($contextName);
        // 真正的 contextName 和 stageName 必须全小写, 用 . 分割.
        $this->stageName = StringUtils::normalizeString($stageName);
        $this->query = $query;
    }

    /*------- next -------*/

    public function toStage(string $stageName) : ucl
    {
        return new self($this->contextName, $stageName, $this->query);
    }

    /*------- create -------*/

    public static function isValid(string $ucl) : bool
    {
        return ContextTypeUtils::isValidUcl($ucl);
    }

    public static function decodeUcl(string $string) : ? Ucl
    {
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

        $prefix = StringUtils::gluePrefixAndName(
            $this->contextName,
            $this->stageName,
            '#'
        );

        $ucl = StringUtils::gluePrefixAndName(
            $prefix,
            empty($this->query) ? '' : json_encode($this->query),
            '?'
        );

        return $ucl;
    }

    /*------- property -------*/

    public function asIntentName() : string
    {
        return StringUtils::gluePrefixAndName(
            $this->contextName,
            $this->stageName,
            Context::NAMESPACE_SEPARATOR
        );
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

    public function findStageDef(Cloner $cloner) : StageDef
    {
        $fullname = $this->parseFullStageName();
        return $cloner
            ->mind
            ->stageReg()
            ->getDef($fullname);
    }

    public function findContextDef(Cloner $cloner) : ContextDef
    {
        return $cloner
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

}