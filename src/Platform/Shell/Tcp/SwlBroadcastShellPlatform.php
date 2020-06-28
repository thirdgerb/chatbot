<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Tcp;

use Commune\Blueprint\Kernel\Handlers\ShellOutputReqHandler;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Platform\Libs\SwlAsync\TcpPacker;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlBroadcastShellPlatform extends SwlDuplexShellPlatform
{


    public function receiveAsyncRequest(string $chan, ShellOutputRequest $request) : void
    {
        $server = $this->getServer();
        $packer = new TcpPacker(
            $this,
            $server,
            0,
            0,
            ''
        );

        $adapter = new SwlBroadcastAdapter(
            $packer,
            $this->getAppId(),
            $request
        );

        $this->onAdapter($packer, $adapter, ShellOutputReqHandler::class);
    }

}