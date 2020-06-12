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
use Commune\Blueprint\Exceptions\CommuneBootingException;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Framework\ServiceRegistrar;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Contracts\Log\LogInfo;
use Commune\Framework\Event\FinishRequest;
use Commune\Framework\Event\StartRequest;
use Commune\Ghost\Bootstrap;
use Commune\Framework\AbsApp;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg\Convo\ApiMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Protocal\ProtocalMatcher;
use Commune\Blueprint\Ghost\Handlers;


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

    /**
     * @var ProtocalMatcher
     */
    protected $requestProtoMatcher;

    /**
     * @var ProtocalMatcher
     */
    protected $apiProtoMatcher;


    public function __construct(
        GhostConfig $config,
        ContainerContract $procC = null,
        ReqContainer $reqC = null,
        ServiceRegistrar $registrar = null,
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

    public function newCloner(InputMsg $input): Cloner
    {
        if (!$this->activated) {
            throw new CommuneBootingException(
                'Ghost not activated'
            );
        }

        // MessageId 应该是唯一的.
        $container = $this->newReqContainerIns($input->getMessageId());

        $cloner = new ICloner($this, $container, $input);

        $container->share(ReqContainer::class, $container);
        $container->share(InputMsg::class, $input);
        $container->share(Comprehension::class, $input->comprehension);
        $container->share(Cloner::class, $cloner);

        // boot 请求容器.
        $this->getServiceRegistrar()->bootReqServices($container);
        return $cloner;
    }

    public function handleRequest(GhostRequest $request): GhostResponse
    {
        try {

            if (!$request->isValid()) {
                return $response = $request->response(AppResponse::BAD_REQUEST);
            }

            $input = $request->getInput();
            $cloner = $this->newCloner($input);
            $cloner->fire(new StartRequest($cloner));

            // 如果是无状态请求.
            if ($request->isStateless()) {
                $cloner->noState();
            }

            $handler = $this->getRequestHandler($cloner->container, $request);

            if (!isset($handler)) {
                return $response = $request
                    ->response(AppResponse::HANDLER_NOT_FOUND);
            }

            // 使用 Handler 来响应.
            $response = $handler($request);

        } catch (\Throwable $e) {
            $this->getExceptionReporter()->report($e);
            $response = $request->response(AppResponse::HOST_LOGIC_ERROR);

        } finally {
            if (isset($cloner)) {
                $cloner->fire(new FinishRequest($cloner));
                $cloner->finish();
            }

            return $response
                ?? $request->response(AppResponse::HOST_LOGIC_ERROR);
        }
    }

    protected function getExceptionReporter() : ExceptionReporter
    {
        return $this->getProcContainer()->get(ExceptionReporter::class);
    }

    /*--------- protocals ---------*/

    public function getRequestHandler(
        ReqContainer $container,
        GhostRequest $request
    ): ? Handlers\GhtRequesthandler
    {
        if (!isset($this->requestProtoMatcher)) {
            $options = $this->getConfig()->requestHandlers;
            $this->requestProtoMatcher = new ProtocalMatcher($options);
        }

        $gen = $this->requestProtoMatcher->matchEach($request);
        return $this->makeHandler($container, $gen);
    }

    public function getApiHandler(ReqContainer $container, ApiMsg $message): ? Handlers\GhtApiHandler
    {
        $matcher = $this->apiProtoMatcher
            ?? $this->apiProtoMatcher = new ProtocalMatcher($this->getConfig()->apiHandlers);

        $gen = $matcher->matchEach($message);
        return $this->makeHandler($container, $gen);
    }

    /**
     * @param ReqContainer $container
     * @param \Generator $gen
     * @return null|callable
     */
    protected function makeHandler(ReqContainer $container, \Generator $gen)
    {
        foreach ($gen as $option) {
            $abstract = $option->handler;
            $params = $option->params;
            $handlerIns = $container->make($abstract, $params);
            return $handlerIns;
        }

        return null;
    }


}