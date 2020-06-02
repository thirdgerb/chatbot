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

use Commune\Ghost\IOperate\OStart;
use Commune\Blueprint\Ghost\Operate\Finale;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneDialogManagerPipe extends AClonePipe
{
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        $next = new OStart($this->cloner);

        try {

            $tracer = $this->cloner->runtime->trace;
            while (isset($next)) {

                $tracer->record($next);

                $next = $next->tick();

                if ($next instanceof Finale) {
                    $next->tick();
                    break;
                }
            }

            return $request->success($this->cloner);

        } catch (CommuneRuntimeException $e) {
            throw $e;

        } catch (\Throwable $e) {
            $this->cloner->logger->error($e);
            throw new BrokenRequestException($e->getMessage(), $e);
        }
    }


}