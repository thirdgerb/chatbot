<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\App;

use Commune\Blueprint\Framework\ReqContainer;
use Commune\Framework\AbsApp;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Protocal\ProtocalMatcher;
use Commune\Support\Protocal\ProtocalOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
abstract class AppForProtocal extends AbsApp
{
    /**
     * @var ProtocalMatcher
     */
    protected $protocalMatcher;

    /**
     * @return ProtocalOption[]
     */
    abstract protected function getProtocalOptions() : array;

    /*------ protocal ------*/

    public function getProtocalMatcher() : ProtocalMatcher
    {
        return $this->protocalMatcher
            ?? $this->protocalMatcher = new ProtocalMatcher(
                $this->getConsoleLogger(),
                $this->getProtocalOptions()
            );
    }

    public function eachProtocalHandler(
        ReqContainer $container,
        Protocal $protocal,
        string $handlerInterface = null
    ): \Generator
    {
        $matcher = $this->getProtocalMatcher();
        foreach ($matcher->matchEach($protocal, $handlerInterface) as $handlerOption) {
            $handler = $handlerOption->handler;
            $params = $handlerOption->params;
            yield $container->make($handler, $params);
        }
    }


}