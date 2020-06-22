<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Stdio;

use Commune\Blueprint\Platform\Adapter;
use Commune\Blueprint\Platform\Packer;
use Commune\Framework\Spy\SpyAgency;
use Commune\Protocals\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioPacker implements Packer
{

    /**
     * @var StdioPlatform
     */
    protected $platform;

    /**
     * @var HostMsg
     */
    protected $message;

    public function __construct(StdioPlatform $platform, HostMsg $message)
    {
        $this->platform = $platform;
        $this->message = $message;
        SpyAgency::incr(static::class);
    }

    public function isInvalid(): ? string
    {
        return null;
    }

    public function getPlatform() : StdioPlatform
    {
        return $this->platform;
    }

    public function getInput() : HostMsg
    {
        return $this->message;
    }

    public function getWriter() : StdioConsole
    {
        return $this->platform->getWriter();
    }

    public function adapt(string $adapterName): Adapter
    {
        return new $adapterName($this);
    }

    public function fail(\Throwable $e): void
    {
        $this->platform->getWriter()->error($e->getMessage());
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}