<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cloner;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Ghost\Cloner\ClonerScene;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Container\ContainerContract;
use Commune\Framework\Spy\SpyAgency;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Ucl $entry
 * @property-read array $env        环境变量.
 */
class IClonerScene implements ClonerScene
{

    protected $_entry;

    protected $_env;

    public function __construct(Ucl $root, array $env)
    {
        $this->_entry = $root;
        $this->_env = $env;
        SpyAgency::incr(static::class);
    }

    public static function factory(ContainerContract $app) : self
    {

        $env = [];
        $root = '';
        if ($app->bound(GhostRequest::class)) {
            /**
             * @var GhostRequest $request
             */
            $request = $app->make(GhostRequest::class);
            $env = $request->getEnv();
            $root = $request->getEntry();
        }

        /**
         * @var GhostConfig $config
         */
        $config = $app->make(GhostConfig::class);
        $scenes = $config->sceneContextNames;

        $entry = Ucl::decode($root);
        $isValid = $entry->isValidPattern()
            && in_array($entry->contextName, $scenes);

        $entry = $isValid
            ? $entry
            : Ucl::decode($config->defaultContextName);

        return new static($entry, $env);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'entry' :
                return $this->_entry;
            case 'env' :
                return $this->_env;
            default:
                return null;
        }
    }

    public function __destruct()
    {
        unset(
            $this->_entry,
            $this->_env
        );

        SpyAgency::decr(static::class);
    }
}