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
use Commune\Support\Utils\StringUtils;

/**
 * Uniform Context Locator
 * 类似 url 的语境定位对象. 用一个 ucl 字符串可以定位一个语境.
 *
 * 格式为:
 * json_encode([
 *  string $contextName,
 *  string $stageName,
 *  array $query
 * ]);
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
class Ucl implements \Serializable
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
        $this->contextName = $contextName;
        $this->stageName = $stageName;
        $this->query = $query;
    }

    public static function decodeUcl(string $string) : ? Ucl
    {
        $arr = static::decodeUclArr($string);
        if (empty($arr)) {
            return null;
        }

        list($contextName, $stageName, $query) = $arr;
        return new static($contextName, $stageName, $query);
    }

    public static function decodeUclArr(string $string) : ? array
    {
        $data = json_decode($string, true);
        if (empty($data) || !is_array($data)) {
            return null;
        }

        $contextName = $data[0] ?? null;
        $stageName = $data[1] ?? null;
        $query = $data[2] ?? null;

        if (
            is_string($contextName)
            && !empty($contextName)
            && is_string($stageName)
            && is_array($query)
        ) {
            return [$contextName, $stageName, $query];
        }

        return null;
    }

    public function toEncodeArr() : array
    {
        return [
            $this->contextName,
            $this->stageName,
            $this->query
        ];
    }

    public function encodeUcl() : string
    {
        return json_encode($this->toEncodeArr());
    }

    public function getContextName() : string
    {
        return $this->contextName;
    }

    public function getStageName() : string
    {
        return $this->stageName;
    }

    public function getContextId() : string
    {
        return sha1(json_encode([
           'contextName' => $this->contextName,
           'query' => $this->query,
        ]));
    }

    public function getQuery() : array
    {
        return $this->query;
    }

    public function fullStageName(string $stage = null) : string
    {
        $stage = $stage ?? $this->stageName;
        return StringUtils::gluePrefixAndName(
            $this->contextName,
            $stage,
            Context::NAMESPACE_SEPARATOR
        );
    }

    public function findStageDef(Cloner $cloner) : StageDef
    {
        $fullname = $this->fullStageName();
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
        return $this->encodeUcl();
    }

    public function serialize()
    {
        return $this->encodeUcl();
    }

    public function unserialize($serialized)
    {
        $arr = static::decodeUclArr($serialized);
        list($contextName, $stageName, $query) = $arr;
        $this->contextName = $contextName;
        $this->stageName = $stageName;
        $this->query = $query;
    }


}