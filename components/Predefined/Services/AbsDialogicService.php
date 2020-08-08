<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined\Services;

use Commune\Blueprint\Ghost\Callables\DialogicService;
use Commune\Blueprint\Ghost\Tools\Deliver;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDialogicService implements DialogicService
{
    protected $auth = [];

    abstract public function handle(array $payload, Deliver $deliver): void;

    public function auth(): array
    {
        return $this->auth;
    }

    public function __invoke(array $payload, Deliver $deliver): void
    {
        try {
            $this->handle($payload, $deliver);

        } catch (\Throwable $e) {

            $deliver
                ->error(
                    __METHOD__
                    . ' failed: '
                    . get_class($e)
                    . ':'
                    . $e->getMessage()
                );
        }
    }


}