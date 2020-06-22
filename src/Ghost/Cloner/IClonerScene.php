<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cloner;

use Commune\Blueprint\Ghost\Cloner\ClonerScene;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Framework\Spy\SpyAgency;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Ucl $entry
 * @property-read array $env        环境变量.
 */
class IClonerScene implements ClonerScene
{

    protected $_entry;

    protected $_env;

    public function __construct(Ucl $root, array $env)
    {
        $this->_entry = $root;
        $this->_env = $env;
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
                return null;
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