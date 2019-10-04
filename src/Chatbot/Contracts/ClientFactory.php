<?php

/**
 * Class ClientFactory
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


use GuzzleHttp\Client;

interface ClientFactory
{
    public function create(array $config) : Client;
}