<?php


namespace Commune\Chatbot\App\Components\Configurable\Drivers;


use Commune\Chatbot\App\Components\Configurable\Configs\DomainConfig;

interface DomainConfigRepository
{

    public function preload() : void;

    /**
     * 预加载所有的资源. 在worker process  boot 的过程中需要全部加载.
     * 根据repository 的不同, resource 的类型也应该有差别.
     *
     * @param $resource
     */
    public function addResource($resource) : void;

    /**
     * @param string $name
     */
    public function newDomain(string $name) : void;

    /**
     * 读取 domain 的名称.
     * @param int $limit
     * @param int $offset
     * @return string[]  names
     */
    public function paginateDomainNames(int $limit = 0, int $offset = 0) : array;

    /**
     * @return int
     */
    public function getDomainCount() : int;

    /**
     * 更新一个domain 的配置, 更新到存储介质中.
     * 如果 domain 不存在, 则要在默认的位置里创建一个.
     *
     * @param DomainConfig $domain
     */
    public function update(DomainConfig $domain) : void;

    /**
     * @param string $domain
     * @return DomainConfig|null
     */
    public function get(string $domain) : ? DomainConfig;

    /**
     * @param string $domain
     * @return bool
     */
    public function has(string $domain) : bool;

    /**
     * @return int
     */
    public function getCount() :int;

    /**
     * 遍历每一个domain
     * @return \Generator
     */
    public function each() : \Generator;

    /**
     * 删除一个元素.
     * @param string $domain
     * @return int
     */
    public function remove(string $domain) : int;

    /**
     * 保存改动.
     */
    public function save() : void;
}