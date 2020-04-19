<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Session;

use Commune\Ghost\Blueprint\Convo\Scene;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sceneName
 * @property-read string $contextName
 * @property-read array $env
 */
class IScene implements Scene
{
    /**
     * @var string
     */
    protected $sceneName;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var array
     */
    protected $env;

    /**
     * IScene constructor.
     * @param string $sceneName
     * @param string $contextName
     * @param array $env
     */
    public function __construct(
        string $sceneName,
        string $contextName,
        array $env
    )
    {
        $this->sceneName = $sceneName;
        $this->contextName = $contextName;
        $this->env = $env;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return null;
    }

}