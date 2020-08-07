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
use Commune\Framework\Session\ASessionScene;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Ucl $entry
 * @property-read array $env        环境变量.
 */
class IClonerScene extends ASessionScene implements ClonerScene
{

    /**
     * @var Cloner
     */
    protected $_cloner;

    public function __construct(Cloner $cloner, Ucl $root, array $env)
    {
        $this->_cloner = $cloner;

        parent::__construct($root, $env);
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
            case 'userId' :
                return $this->_cloner->input->getCreatorId();
            case 'userName' :
                return $this->_cloner->input->getCreatorName();
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
            default:
                return parent::__get($name);
        }
    }

    public function __destruct()
    {
        unset(
            $this->_cloner
        );
        parent::__destruct();
    }
}