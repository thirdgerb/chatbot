<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Coms\Broadcaster;

use Commune\Blueprint\Platform;
use Swoole\Server;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BroadcasterPlatform implements Platform
{
    public function serve(): void
    {
        $server = new Server();


        $server->on('receive', function($serv, $fd, $from_id, $data) {
            //投递异步任务
            $task_id = $serv->task($data);
            echo "Dispatch AsyncTask: id=$task_id\n";
        });
    }


    public function onSubscribe($fd, string $shellId, string $sessionId)
    {

    }

    public function onClose($fd)
    {

    }

    public function onPublish(
        string $shellId,
        string $sessionId,
        string $batchId,
        string $traceId
    )
    {

    }

}