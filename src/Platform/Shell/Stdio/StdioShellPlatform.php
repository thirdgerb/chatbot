<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Stdio;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Platform;
use Commune\Blueprint\Shell;
use Commune\Platform\AbsPlatform;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioShellPlatform extends AbsPlatform
{

    /**
     * @var Shell
     */
    protected $shell;

    public function getAppId(): string
    {
        return $this->shell->getId();
    }

    public function serve(): void
    {
        // TODO: Implement serve() method.
    }

    public function sleep(float $seconds): void
    {
        // TODO: Implement sleep() method.
    }

    public function shutdown(): void
    {
        // TODO: Implement shutdown() method.
    }

    protected function handleRequest(
        Platform\Adapter $adapter,
        AppRequest $request,
        string $interface = null
    ): void
    {
        // TODO: Implement handleRequest() method.
    }


}