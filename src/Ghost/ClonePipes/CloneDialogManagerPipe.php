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
    protected function doHandle(GhostRequest $request, \Closure $current): GhostResponse
    {
        $current = new OStart($this->cloner);
        $tracer = $this->cloner->runtime->trace;

        try {

            while (isset($current)) {

                $next = $current->tick();
                $tracer->record($current);
                if ($current instanceof Finale) {
                    break;
                }

                $current = $next;
            }

            unset($current);
            unset($next);

        } catch (CommuneRuntimeException $e) {
            throw $e;

        } catch (\Throwable $e) {
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