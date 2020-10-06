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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class IClonerScene implements ClonerScene
{
    /**
     * @var string
     */
    protected $scene;

    /**
     * @var Ucl
     */
    protected $entry;

    /**
     * @var string
     */
    protected $root;

    public function __construct(string $scene, string $root)
    {
        $this->scene = $scene;
        $this->root = $root;
    }

    public static function factory(ContainerContract $app) : self
    {
        $scene = '';
        $root = '';
        if ($app->bound(GhostRequest::class)) {
            /**
             * @var GhostRequest $request
             */
            $request = $app->make(GhostRequest::class);
            $root = $request->getEntry();
            $scene = $request->getInput()->getScene();
        }

        $root = empty($root) ? static::defaultEntry($app) : $root;
        return new static($scene, $root);
    }

    public static function defaultEntry(ContainerContract $app) : Ucl
    {
        /**
         * @var GhostConfig $config
         */
        $config = $app->make(GhostConfig::class);
        return Ucl::decode($config->defaultContextName);
    }

    public function getName(): string
    {
        return $this->scene;
    }

    public function getEntry(): Ucl
    {
        return $this->entry ?? $this->entry
            = Ucl::decode($this->root);
    }


}