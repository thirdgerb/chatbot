<?php

/**
 * Class BaseServiceProvider
 * @package Commune\Chatbot\Framework\Providers
 */

namespace Commune\Chatbot\Framework\Providers;

use Commune\Chatbot\Blueprint\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    const IS_REACTOR_SERVICE_PROVIDER = false;

}