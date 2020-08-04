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
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerScene;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Container\ContainerContract;
use Commune\Contracts\Trans\Translator;
use Commune\Framework\Spy\SpyAgency;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Ucl $entry
 * @property-read array $env        环境变量.
 */
class IClonerScene implements ClonerScene
{

    /**
     * @var Ucl
     */
    protected $_entry;

    /**
     * @var array
     */
    protected $_env;

    /**
     * @var Cloner
     */
    protected $_cloner;

    public function __construct(Cloner $cloner, Ucl $root, array $env)
    {
        $this->_cloner = $cloner;
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

        $cloner = $app->make(Cloner::class);
        return new static($cloner, $entry, $env);
    }

    protected function getGhostRequest() : ? GhostRequest
    {
        $container = $this->_cloner->container;
        return $container->bound(GhostRequest::class)
            ? $container->get(GhostRequest::class)
            : null;
    }

    public function __get($name)
    {

        switch ($name) {
            case 'entry' :
                return $this->_entry;
            case 'env' :
                return $this->_env;
            case 'userLevel' :
                return $this->_env[ClonerScene::ENV_USER_LEVEL] ?? 0;
            case 'userInfo' :
                return $this->_env[ClonerScene::ENV_USER_INFO] ?? [];
            case 'conversationId' :
                return $this->_cloner->getConversationId();
            case 'scene' :
                return $this->_cloner->input->getScene();
            case 'sessionId' :
                return $this->_cloner->getSessionId();
            case 'fromApp' :
                $request = $this->getGhostRequest();
                return isset($request) ? $request->getFromApp() : '';
            case 'fromSession' :
                $request = $this->getGhostRequest();
                return isset($request) ? $request->getFromSession() : '';
            case 'lang' :
                return $this->_env[ClonerScene::ENV_LANG]
                    ?? $this->_cloner
                        ->container
                        ->get(Translator::class)
                        ->getDefaultLocale();

            default:
                return $this->_env[$name] ?? null;
        }
    }

    public function __destruct()
    {
        unset(
            $this->_entry,
            $this->_env,
            $this->_cloner
        );

        SpyAgency::decr(static::class);
    }
}