<?php

/**
 * Class GuzzleClientFactory
 * @package Commune\Chatbot\Framework\Utils
 */

namespace Commune\Chatbot\Framework\Predefined;


use Commune\Chatbot\Contracts\ClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class GuzzleClientFactory implements ClientFactory
{
    public function create(array $config): Client
    {
        // 防止阻塞, 加个默认的 timeout
        $config[RequestOptions::TIMEOUT] = $config[RequestOptions::TIMEOUT] ?? 0.5;
        return new Client($config);
    }


}