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
     * @var string
     */
    protected $line;

    /**
     * StdioPacker constructor.
     * @param StdioPlatform $platform
     * @param string $line
     */
    public function __construct(StdioPlatform $platform, string $line)
    {
        $this->platform = $platform;
        $this->line = $line;
    }

    public function isInvalid(): ? string
    {
        return null;
    }

    public function getPlatform() : StdioPlatform
    {
        return $this->platform;
    }

    public function getInput() : string
    {
        return $this->line;
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
}