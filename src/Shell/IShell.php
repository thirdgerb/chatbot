<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell;

use Commune\Blueprint\Configs\ShellConfig;
use Commune\Blueprint\Exceptions\CommuneBootingException;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistry;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Kernel\Protocals\AppProtocal;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Framework\App\AbsAppKernel;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Shell\Bootstrap;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShell extends AbsAppKernel implements Shell
{
    protected $bootstrappers = [
        // 注册配置 Option 单例到进程中.
        Bootstrap\ShellLoadConfigOption::class,
        // 注册相关服务
        Bootstrap\ShellRegisterProviders::class,
        // 注册相关组件
        Bootstrap\ShellLoadComponents::class
    ];

    /**
     * @var ShellConfig
     */
    protected $config;


    public function __construct(
        ShellConfig $config,
        ContainerContract $procC = null,
        ReqContainer $reqC = null,
        ServiceRegistry $registrar = null,
        ConsoleLogger $consoleLogger = null,
        LogInfo $logInfo = null
    )
    {
        $this->config = $config;
        parent::__construct($procC, $reqC, $registrar, $consoleLogger, $logInfo);
    }


    public function getId(): string
    {
        return $this->config->id;
    }

    public function getName(): string
    {
        return $this->config->name;
    }

    public function getConfig(): ShellConfig
    {
        return $this->config;
    }

    protected function basicBindings(): void
    {
        $this->instance(ShellConfig::class, $this->config);
        $this->instance(Shell::class, $this);
    }

    /*------- session -------*/
    protected function getProtocalOptions(): array
    {
        return $this->getConfig()->protocals;
    }

    protected function makeSession(ReqContainer $container, InputMsg $input): Session
    {
        if (!$this->activated) {
            throw new CommuneBootingException(
                'shell not activated'
            );
        }

        $session = new IShellSession($this, $container, $input);

        $container->share(ReqContainer::class, $container);
        $container->share(InputMsg::class, $input);
        $container->share(Comprehension::class, $input->comprehension);
        $container->share(ShellSession::class, $session);

        // boot 请求容器.
        $this->getServiceRegistry()->bootReqServices($container);

        return $session;
    }


    protected function isValidFinaleResponse(AppProtocal $protocal): bool
    {
        return $protocal instanceof ShellOutputResponse;
    }

}