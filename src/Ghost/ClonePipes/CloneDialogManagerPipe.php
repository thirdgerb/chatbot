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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Framework\Request\AppResponse;
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
        $tracer = $this->cloner->runtime->trace;

        try {

            while (isset($next)) {

                $tracer->record($next);

                $next = $next->tick();

                if ($next instanceof Finale) {
                    $next->tick();
                    $next = null;
                }
            }

        } catch (CommuneRuntimeException $e) {
            throw $e;

        } catch (\Throwable $e) {
            $this->cloner->logger->error($e);
            throw new BrokenRequestException($e->getMessage(), $e);

        } finally {
            // 调试模式下检查运行轨迹.
            if (CommuneEnv::isDebug()) {
                $tracer->log($this->logger);
            }
        }

        return $request->success($this->cloner);
    }


}