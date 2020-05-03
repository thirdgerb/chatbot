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
     * @param string[] $query
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
            return new static($contextName, $stageName, $query);
        }

        return null;
    }

    public function encodeUcl() : string
    {
        return json_encode([
            $this->contextName,
            $this->stageName,
            $this->query
        ]);
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

    public function __get($name)
    {
        return $this->{$name};
    }

    public function __toString() : string
    {
        return $this->encodeUcl();
    }
}