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

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Ghost\Callables\DialogicService;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerDispatcher;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Predefined\Context\AsyncServiceContext;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\HostMsg\Convo\ContextMsg;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerDispatcher implements ClonerDispatcher, HasIdGenerator
{
    use IdGeneratorHelper;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * IClonerDispatcher constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
    }


    public function asyncService(string $service, array $params): void
    {
        $expect = DialogicService::class;
        if (!is_a($service, $expect, true)) {
            throw new CommuneLogicException("expect $expect, $service given");
        }

        $ucl = AsyncServiceContext::genUcl([
            'service' => $service,
        ]);

        /**
         * @var AsyncServiceContext $context
         */
        $context = $ucl->findContext($this->cloner);
        $context->payload = $params;

        $this->yieldContext(
            $this->cloner->getSessionId(),
            $context,
            ContextMsg::MODE_REDIRECT,
            $this->createUuId()
        );
    }

    public function asyncJob(Ucl $job): void
    {
        $context = $job->findContext($this->cloner);
        $this->yieldContext(
            $this->cloner->getSessionId(),
            $context,
            ContextMsg::MODE_BLOCKING,
            $this->createUuid()
        );
    }

    public function yieldContext(
        string $sessionId,
        Context $context,
        int $mode = ContextMsg::MODE_BLOCKING,
        string $convoId = ''
    ): void
    {
        $message = $context->toContextMsg()->withMode($mode);
        $avatar = $this->cloner->avatar;
        $input = IInputMsg::instance(
            $message,
            $sessionId,
            $avatar->getId(),
            $avatar->getName(),
            $convoId
        );
        $this->cloner->asyncInput($input);
    }


}