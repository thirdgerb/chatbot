<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Platforms\ReactStdio;

use Commune\Framework\Prototype\Server\AReactStdioServer;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Contracts\ShlServer;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RSServer extends AReactStdioServer implements ShlServer
{

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * RSServer constructor.
     * @param Shell $shell
     */
    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
        parent::__construct();
    }


    public function handleData(string $data): void
    {
    }


}