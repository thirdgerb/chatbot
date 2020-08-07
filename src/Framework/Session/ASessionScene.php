<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Session;

use Commune\Blueprint\Framework\Session\SessionScene;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Framework\Spy\SpyAgency;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ASessionScene implements SessionScene
{

    /**
     * @var array
     */
    protected $_env;

    /**
     * @var Ucl
     */
    protected $_entry;

    /**
     * ASessionScene constructor.
     * @param Ucl $_entry
     * @param array $_env
     */
    public function __construct(Ucl $_entry, array $_env)
    {
        $this->_env = $_env;
        $this->_entry = $_entry;
        SpyAgency::incr(static::class);
    }

    public function __get($name)
    {

        switch ($name) {
            case 'entry' :
                return $this->_entry;
            case 'env' :
                return $this->_env;
            default:
                return $this->_env[$name] ?? null;
        }
    }

    public function __destruct()
    {
        unset(
            $this->_entry,
            $this->_env
        );

        SpyAgency::decr(static::class);
    }

}