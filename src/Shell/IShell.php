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
use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Commune\Blueprint\Framework\ProcContainer;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistry;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Kernel\Protocals\AppProtocal;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\InputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Framework\App\AbsAppKernel;
use Commune\Kernel\Protocals\IShellOutputResponse;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Shell\Bootstrap;
use Commune\Support\Utils\TypeUtils;


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


    /**
     * IShell constructor.
     * @param ShellConfig $config
     * @param ProcContainer|null $procC
     * @param ReqContainer|null $reqC
     * @param ServiceRegistry|null $registrar
     * @param ConsoleLogger|null $consoleLogger
     * @param LogInfo|null $logInfo
     */
    public function __construct(
        ShellConfig $config,
        ProcContainer $procC = null,
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
        return 'shell:' . $this->config->id;
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
        $protocals = $this->getConfig()->protocals;
        return $protocals;
    }

    protected function failResponse(
        string $traceId,
        int $errcode = AppResponse::HOST_REQUEST_FAIL,
        string $errmsg = ''
    ): AppResponse
    {
        return new IShellOutputResponse([
            'traceId' => $traceId,
            'errcode' => $errcode,
            'errmsg' => $errmsg
        ]);

    }

    protected function validateAppRequest(AppRequest $request): void
    {
        $valid = $request instanceof ShellInputRequest
            || $request instanceof ShellOutputRequest;

        if (!$valid) {
            $actual = TypeUtils::getType($request);

            throw new CommuneRuntimeException(
                "bad request, expect "
                . ShellInputRequest::class
                . ' or '
                . ShellOutputRequest::class
                . ", $actual given"
            );
        }
    }


    public function newSession(ReqContainer $container, string $sessionId): ShellSession
    {
        if (!$this->activated) {
            throw new CommuneBootingException(
                'shell not activated'
            );
        }

        return new IShellSession($this, $container, $sessionId);
    }


    protected function makeSession(ReqContainer $container, AppRequest $request): Session
    {
        if (!$this->activated) {
            throw new CommuneBootingException(
                'shell not activated'
            );
        }

        $container->share(ReqContainer::class, $container);
        $container->share(AppRequest::class, $request);
        $container->share(get_class($request), $request);

        if ($request instanceof InputRequest) {
            $input = $request->getInput();
            $container->share(InputMsg::class, $input);
            $container->share(Comprehension::class, $request->getComprehension());
            $container->share(HostMsg::class, $input->getMessage());
        }

        if ($request instanceof ShellInputRequest) {
            $container->share(ShellInputRequest::class, $request);
        }

        if ($request instanceof ShellOutputRequest) {
            $container->share(ShellOutputRequest::class, $request);
        }


        $sessionId = $request->getSessionId();

        $session = $this->newSession($container, $sessionId);
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