<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ClonePipes;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Dialog\IStartProcess;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Blueprint\Exceptions\HostRuntimeException;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneDialogManagerPipe extends AClonePipe
{
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        $nextDialog = $dialog ?? new IStartProcess($this->cloner);

        try {

            $tracer = $this->cloner->runtime->trace;

            while(isset($nextDialog)) {

//                $tracer->record($nextDialog);
//
//                dd(123);
//                $nextDialog = $nextDialog->tick();
//
//                if ($next instanceof Dialog\Finale) {
//                    $next->tick();
//                    break;
//                }
            }
            return $request->success($this->cloner);

        } catch (HostRuntimeException $e) {
            throw $e;

        } catch (\Throwable $e) {
            $this->cloner->logger->error($e);
            throw new BrokenRequestException('', $e);
        }
    }


}