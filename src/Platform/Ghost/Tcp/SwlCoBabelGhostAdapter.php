<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost\Tcp;

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Platform\Libs\SwlCo\TcpAdapterAbstract;
use Commune\Support\Babel\Babel;
use Commune\Support\Babel\BabelSerializable;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlCoBabelGhostAdapter extends TcpAdapterAbstract
{
    protected function unserialize(string $input) : ? AppRequest
    {
        $un = Babel::unserialize($input);
        if ($un instanceof AppRequest) {
            return $un;
        }

        $type = TypeUtils::getType($un);

        $this->error = "babel unserialize expect AppRequest, $type given";
        return null;
    }

    protected function getRequestInterface(): string
    {
        return GhostRequest::class;
    }

    protected function getResponseInterface(): string
    {
        return GhostResponse::class;
    }

    protected function checkWouldClose(AppResponse $response): void
    {
    }


    /**
     * @param GhostResponse $response
     * @return string
     */
    protected function serialize($response) : string
    {
        if (!$response instanceof BabelSerializable) {
            $type = TypeUtils::getType($response);
            throw new CommuneLogicException(
                static::class . '::'. __FUNCTION__
                . ' only accept '
                . BabelSerializable::class
                . ' ghost response, '
                . $type
                . ' given'
            );

        }
        return Babel::serialize($response);
    }


}