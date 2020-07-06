<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\Stdio;

use Clue\React\Stdio\Stdio;
use Commune\Blueprint\Platform;
use Commune\Blueprint\Platform\Adapter;
use Commune\Blueprint\Platform\Packer;
use Commune\Framework\Log\IConsoleLogger;
use Psr\Log\LogLevel;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioPacker implements Packer
{
    /**
     * @var bool
     */
    public $quit = false;

    /**
     * @var Stdio
     */
    public $stdio;

    /**
     * @var Platform
     */
    public $platform;

    /**
     * @var string
     */
    public $creatorId;

    /**
     * @var string
     */
    public $creatorName;

    /**
     * @var string
     */
    public $line;

    /**
     * StdioPacker constructor.
     * @param Stdio $stdio
     * @param Platform $platform
     * @param string $creatorId
     * @param string $creatorName
     * @param string $line
     */
    public function __construct(
        Stdio $stdio,
        Platform $platform,
        string $creatorId,
        string $creatorName,
        string $line
    )
    {
        $this->stdio = $stdio;
        $this->platform = $platform;
        $this->creatorId = $creatorId;
        $this->creatorName = $creatorName;
        $this->line = $line;
    }


    public function isInvalid(): ? string
    {
        return null;
    }

    public function adapt(string $adapterName, string $appId): Adapter
    {
        return new $adapterName($this, $appId);
    }

    public function fail(string $error): void
    {
        $error = IConsoleLogger::wrapMessage(LogLevel::ERROR, $error);
        $this->stdio->write($error);
        $this->quit = true;
    }

    public function destroy(): void
    {
        unset(
            $this->stdio,
            $this->platform
        );
    }


}