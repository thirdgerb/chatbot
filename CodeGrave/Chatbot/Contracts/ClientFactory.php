<?php

/**
 * Class ClientFactory
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


use GuzzleHttp\Client;

/**
 * Guzzle Client 的 factory
 * 虽然 http 请求确定用 guzzle 做客户端
 * 但由于使用协程, 连接池等原因的考虑, 还是用一个Factory
 */
interface ClientFactory
{
    public function create(array $config) : Client;
}