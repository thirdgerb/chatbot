<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Session;

use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Contracts;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Shell\Blueprint\Session\ShlSessionLogger;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 */
class IShlSession implements ShlSession
{

    const INJECTABLE_PROPERTIES = [
        'cache' => Contracts\Cache::class,
        'messenger' => Contracts\Messenger::class,
        'logger' => ShlSessionLogger::class,
        'request' => ShlRequest::class,
        'response' => ShlResponse::class,
        'shell' => Shell::class,
    ];

    /**
     * @var ReqContainer
     */
    protected $container;

    /*------ cached ------*/

    protected $sessionId;

    public function getId(): string
    {
        if (isset($this->sessionId)) {
            return $this->sessionId;
        }

        $id = $this->request->fetchSessionId();
        if (isset($id)) {
            return $this->sessionId = $id;
        }


    }


    /*------ getter ------*/

    public function __get($name)
    {
        if ($name === 'container') {
            return $this->container;
        }

        $injectable = static::INJECTABLE_PROPERTIES[$name] ?? null;
        if (!empty($injectable)) {
            return $this->container->get($injectable);
        }

        return null;
    }

}