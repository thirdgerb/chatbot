<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ProtocalHandlers;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Message\Host\Convo\IText;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostRequestHandler
{

    protected $middleware = [

    ];

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * RequestHandler constructor.
     * @param Cloner $cloner
     * @param array|null $middleware
     */
    public function __construct(Cloner $cloner, array $middleware = null)
    {
        $this->cloner = $cloner;
        $this->middleware = $middleware ?? $this->middleware;
    }


    public function __invoke(GhostRequest $request) : GhostResponse
    {
        $output = $this->cloner->ghostInput->output(new IText('hello world'));
        $this->cloner->output($output);
        return $request->response($this->cloner);

    }

}