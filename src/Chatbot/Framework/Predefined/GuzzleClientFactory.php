<?php

/**
 * Class GuzzleClientFactory
 * @package Commune\Chatbot\Framework\Utils
 */

namespace Commune\Chatbot\Framework\Predefined;


use Commune\Chatbot\Contracts\ClientFactory;
use GuzzleHttp\Client;

class GuzzleClientFactory implements ClientFactory
{
    public function create(array $config): Client
    {
        return new Client($config);
    }


}