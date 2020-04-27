<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Exceptions\HostBootingException;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Container\ContainerContract;
use Commune\Framework\AbsApp;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\GhostInput;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhost extends AbsApp implements Ghost
{

    /**
     * @var GhostConfig
     */
    protected $config;

    /**
     * IGhost constructor.
     * @param GhostConfig $config
     * @param ContainerContract|null $procC
     * @param App|null $app
     * @param bool $debug
     */
    public function __construct(
        GhostConfig $config,
        ContainerContract $procC = null,
        App $app = null,
        bool $debug = false
    )
    {
        $this->config = $config;
        $set = isset($app);

        parent::__construct(
            $set ? $app->isDebugging() : $debug,
            $set ? $app->getProcContainer() : $procC,
            $set ? $app->getReqContainer() : null,
            $set ? $app->getServiceRegistrar() : null,
            $set ? $app->getConsoleLogger() : null,
            $set ? $app->getLogInfo() : null
        );
    }

    public function getName(): string
    {
        return $this->config->name;
    }

    public function getId(): string
    {
        return $this->config->id;
    }


    protected function basicBindings(): void
    {
        $this->instance(GhostConfig::class, $this->config);
        $this->instance(Ghost::class, $this);
    }

    public function getConfig(): GhostConfig
    {
        return $this->config;
    }

    public function newCloner(GhostInput $input): Cloner
    {
        if (!$this->activated) {
            throw new HostBootingException(
                'Ghost not activated'
            );
        }
        // MessageId 应该是唯一的.
        $container = $this->newReqContainerInstance($input->messageId);

        $cloner = new ICloner($this, $container, $input);

        $container->share(ReqContainer::class, $container);
        $container->share(GhostInput::class, $input);
        $container->share(Comprehension::class, $input->comprehension);
        $container->share(Cloner::class, $cloner);
        $container->share(Session::class, $cloner);

        // boot 请求容器.
        $this->getServiceRegistrar()->bootReqServices($container);

        return $cloner;
    }




}