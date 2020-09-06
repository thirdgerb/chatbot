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
use Commune\Support\Utils\StringUtils;

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

        $entry = static::matchSceneEntry($root, $app);
        $cloner = $app->make(Cloner::class);
        return new static($cloner, $entry, $env);
    }


    public static function defaultEntry(
        ContainerContract $app
    ) : Ucl
    {

        /**
         * @var GhostConfig $config
         */
        $config = $app->make(GhostConfig::class);
        return Ucl::decode($config->defaultContextName);
    }

    public static function matchSceneEntry(
        string $root,
        ContainerContract $app
    ) : Ucl
    {
        if (empty($root)) {
            return static::defaultEntry($app);
        }

        /**
         * @var GhostConfig $config
         */
        $config = $app->make(GhostConfig::class);
        $scenes = $config->sceneContextNames;
        $entry = Ucl::decode($root);

        if (!$entry->isValidPattern()) {
            return static::defaultEntry($app);
        }

        $allowEntry = in_array('*', $scenes)
            || in_array($entry->contextName, $scenes);

        if ($allowEntry) {
            return $entry;
        }

        if (static::allowWildcardEntry($root, $scenes)) {
            return $entry;
        }

        return static::defaultEntry($app);
    }

    public static function allowWildcardEntry(
        string $root,
        array $scenes
    ) : bool
    {
        foreach ($scenes as $scene) {
            if (
                StringUtils::isWildcardPattern($scene)
                && StringUtils::wildcardMatch($scene, $root)
            ) {
                return true;
            }
        }

        return false;
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