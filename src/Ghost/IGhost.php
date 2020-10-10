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

use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Commune\Blueprint\Framework\ProcContainer;
use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Ghost\Bootstrap;
use Commune\Framework\App\AbsAppKernel;
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Exceptions\CommuneBootingException;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Kernel\Protocols\AppProtocol;
use Commune\Blueprint\Framework\ServiceRegistry;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Kernel\Protocols\GhostResponse;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Kernel\Protocols\IGhostResponse;
use Commune\Protocols\Comprehension;
use Commune\Protocols\HostMsg;
use Commune\Protocols\Intercom\InputMsg;
use Commune\Support\Protocol\ProtocolMatcher;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhost extends AbsAppKernel implements Ghost
{

    protected $bootstrappers = [
        // 注册配置 Option 单例到进程中.
        Bootstrap\GhostLoadConfigOption::class,
        // 注册相关服务
        Bootstrap\GhostRegisterProviders::class,
        // 注册相关组件
        Bootstrap\GhostLoadComponent::class,
    ];

    /**
     * @var GhostConfig
     */
    protected $config;

    /**
     * @var ProtocolMatcher
     */
    protected $requestProtoMatcher;

    /**
     * @var ProtocolMatcher
     */
    protected $apiProtoMatcher;


    public function __construct(
        GhostConfig $config,
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

    /**
     * @param ReqContainer $container
     * @param string $sessionId
     * @param string|null $convoId
     * @return Cloner
     */
    public function newCloner(
        ReqContainer $container,
        string $sessionId,
        string $convoId = null
    ): Cloner
    {
        if (!$this->activated) {
            throw new CommuneBootingException(
                'Ghost not activated'
            );
        }

        $cloner = new ICloner($this, $container, $sessionId, $convoId);

        return $cloner;
    }

    protected function makeSession(ReqContainer $container, AppRequest $request): Session
    {
        // share
        $container->share(AppRequest::class, $request);
        $container->share(get_class($request), $request);

        $convoId = null;

        if ($request instanceof GhostRequest) {
            $input = $request->getInput();
            $convoId = $input->getConvoId();
            $container->share(GhostRequest::class, $request);
            $container->share(InputMsg::class, $input);
            $container->share(Comprehension::class, $request->getComprehension());
            $container->share(HostMsg::class, $input->getMessage());
        }

        $sessionId = $request->getSessionId();
        $cloner = $this->newCloner($container, $sessionId, $convoId);
        $container->share(Cloner::class, $cloner);

        return $cloner;
    }

    /*--------- kernel ---------*/

    protected function failResponse(
        string $traceId,
        int $errcode = AppResponse::HOST_REQUEST_FAIL,
        string $errmsg = ''
    ): AppResponse
    {
        return new IGhostResponse([
            'traceId' => $traceId,
            'errcode' => $errcode,
            'errmsg' => $errmsg
        ]);
    }

    protected function validateAppRequest(AppRequest $request): void
    {
        $valid = $request instanceof GhostRequest;

        if (!$valid) {
            $actual = TypeUtils::getType($request);

            throw new CommuneRuntimeException(
               "bad request, expect "
                . GhostRequest::class
                . ", $actual given"
            );
        }
    }


    /*--------- Protocols ---------*/

    protected function getProtocolOptions(): array
    {
        return $this->getConfig()->Protocols;
    }

    protected function isValidFinaleResponse(AppProtocol $Protocol): bool
    {
        return $Protocol instanceof GhostResponse;
    }
}