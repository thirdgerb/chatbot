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

use Commune\Blueprint\Ghost\Cloner\ClonerEnv;
use Commune\Blueprint\Ghost\Cloner\ClonerGuest;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Container\ContainerContract;
use Commune\Support\Arr\TArrayData;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerEnv implements ClonerEnv
{
    use TArrayData;

    const DEFAULTS = [
        ClonerEnv::USER_INFO_KEY => [],
        ClonerEnv::USER_LEVEL_KEY => ClonerGuest::GUEST,
        ClonerEnv::BOT_INFO_KEY => [],
    ];

    public function __construct(array $env)
    {
        $this->_data = $env;
    }

    public static function factory(ContainerContract $container) : self
    {
        $data = self::DEFAULTS;
        if ($container->bound(GhostRequest::class)) {
            /**
             * @var GhostRequest $request
             */
            $request = $container->make(GhostRequest::class);
            $data = $request->getEnv() + $data;
        }

        return new static($data);
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function getData(): array
    {
        return $this->_data;
    }


}