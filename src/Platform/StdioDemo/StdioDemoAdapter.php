<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\StdioDemo;

use Clue\React\Stdio\Stdio;
use Commune\Blueprint\Platform\PlatformAdapter;
use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioDemoAdapter implements PlatformAdapter
{

    /**
     * @var StdioDemoPlatform
     */
    protected $platform;

    /**
     * @var Stdio
     */
    protected $stdio;

    /**
     * @var string
     */
    protected $line;

    /**
     * StdioDemoAdapter constructor.
     * @param Stdio $stdio
     * @param string $line
     */
    public function __construct(Stdio $stdio, string $line)
    {
        $this->stdio = $stdio;
        $this->line = $line;
    }


    public function getRequest(): ShellRequest
    {
        // TODO: Implement getRequest() method.
    }

    public function sendResponse(): ShellResponse
    {
        // TODO: Implement sendResponse() method.
    }


}