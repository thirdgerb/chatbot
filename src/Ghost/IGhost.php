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
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistrar;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Ghost\Bootstrap;
use Commune\Framework\AbsApp;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\GhostInput;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhost extends AbsApp implements Ghost
{
    protected $bootstrappers = [
        // 注册配置 Option 单例到进程中.
        Bootstrap\GhostLoadConfigOption::class,
        // 注册相关服务
        Bootstrap\GhostRegisterProviders::class,
        // 注册相关组件
        Bootstrap\GhostLoadComponent::class,
        // 检验默认的组件是否都实现了绑定
        Bootstrap\GhostContractsValidator::class,
    ];

    /**
     * @var GhostConfig
     */
    protected $config;


    public function __construct(
        GhostConfig $config,
        bool $debug,
        ContainerContract $procC = null,
        ReqContainer $reqC = null,
        ServiceRegistrar $registrar = null,
        ConsoleLogger $consoleLogger = null,
        LogInfo $logInfo = null
    )
    {
        $this->config = $config;
        parent::__construct($debug, $procC, $reqC, $registrar, $consoleLogger, $logInfo);
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
        $container = $this->newReqContainerInstance($input->getMessageId());

        $cloner = new ICloner($this, $container, $input);

        $container->share(ReqContainer::class, $container);
        $container->share(GhostInput::class, $input);
        $container->share(HostMsg::class, $input->getMessage());
        $container->share(Comprehension::class, $input->comprehension);
        $container->share(Cloner::class, $cloner);
        $container->share(Session::class, $cloner);

        // boot 请求容器.
        $this->getServiceRegistrar()->bootReqServices($container);

        return $cloner;
    }




}