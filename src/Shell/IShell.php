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
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Framework\ServiceRegistrar;
use Commune\Blueprint\Shell;
use Commune\Blueprint\Shell\Handlers\InputMessageParser;
use Commune\Blueprint\Shell\Handlers\ShlApiHandler;
use Commune\Blueprint\Shell\Handlers\ShellRequestHandler;
use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Contracts\Log\LogInfo;
use Commune\Framework\AbsApp;
use Commune\Framework\Event\FinishRequest;
use Commune\Framework\Event\StartRequest;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Protocals\HostMsg\Convo\ApiMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Protocal\ProtocalMatcher;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShell extends AbsApp implements Shell
{

    /**
     * @var ShellConfig
     */
    protected $config;

    /**
     * @var ProtocalMatcher
     */
    protected $shellReqHandlerMatcher;

    /**
     * @var ProtocalMatcher
     */
    protected $apiProtoMatcher;

    /**
     * @var ProtocalMatcher
     */
    protected $inputParserMatcher;

    /**
     * @var Shell\Render\RenderManager
     */
    protected $renderManager;

    public function __construct(
        ShellConfig $config,
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

    public function newSession(InputMsg $input): ShellSession
    {
        if (!$this->activated) {
            throw new CommuneBootingException(
                'shell not activated'
            );
        }

        // MessageId 应该是唯一的.
        $container = $this->newReqContainerIns('shl:' . $input->getMessageId());

        $session = new IShellSession($this, $container, $input);

        $container->share(ReqContainer::class, $container);
        $container->share(InputMsg::class, $input);
        $container->share(Comprehension::class, $input->comprehension);
        $container->share(ShellSession::class, $session);

        // boot 请求容器.
        $this->getServiceRegistrar()->bootReqServices($container);

        return $session;
    }

    /*------- protocals -------*/

    public function handleRequest(ShellRequest $request): ShellResponse
    {
        try {

            if (!$request->isValid()) {
                return $response = $request->response(AppResponse::BAD_REQUEST);
            }

            $input = $request->getInput();
            $session = $this->newSession($input);

            $session->fire(new StartRequest($session));

            // 如果是无状态请求.
            if ($request->isStateless()) {
                $session->noState();
            }

            $handler = $this->getRequestHandler($session->container, $request);

            if (!isset($handler)) {
                return $response = $request->response(AppResponse::HANDLER_NOT_FOUND);
            }

            // 使用 Handler 来响应.
            $response = $handler($request);

        } catch (\Throwable $e) {
            $this->getExceptionReporter()->report($e);
            $response = $request->response(AppResponse::HOST_LOGIC_ERROR);

        } finally {
            if (isset($session)) {
                $session->fire(new FinishRequest($session));
                $session->finish();
            }

            return $response
                ?? $request->response(AppResponse::HOST_LOGIC_ERROR);
        }
    }

    public function getRequestHandler(
        ReqContainer $container,
        ShellRequest $request
    ): ? ShellRequestHandler
    {
        if (!isset($this->shellReqHandlerMatcher)) {
            $options = $this->getConfig()->requestHandlers;
            $this->shellReqHandlerMatcher = new ProtocalMatcher($options);
        }

        $gen = $this->shellReqHandlerMatcher->matchEach($request);
        return $this->makeHandler($container, $gen);
    }


    public function getApiHandler(ReqContainer $container, ApiMsg $message): ? ShlApiHandler
    {
        $apiProtoMatcher = $this->apiProtoMatcher ?? $this->apiProtoMatcher
                = new ProtocalMatcher($this->getConfig()->apiHandlers);

        $gen = $apiProtoMatcher->matchEach($message);
        return $this->makeHandler($container, $gen);
    }

    public function getInputParser(ReqContainer $container, HostMsg $message): ? InputMessageParser
    {
        $inputParserMatcher = $this->inputParserMatcher ?? $this->inputParserMatcher
                = new ProtocalMatcher($this->getConfig()->inputParsers);

        $gen = $inputParserMatcher->matchEach($message);
        return $this->makeHandler($container, $gen);
    }

    public function getRenderManager() : Shell\Render\RenderManager
    {
        return $this->renderManager
            ?? $this->renderManager = $this->procC->get(Shell\Render\RenderManager::class);
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


    protected function getExceptionReporter() : ExceptionReporter
    {
        return $this->getProcContainer()->get(ExceptionReporter::class);
    }

}