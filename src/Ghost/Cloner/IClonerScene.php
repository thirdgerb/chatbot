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
 * @property-read Ucl $root
 * @property-read array $env        环境变量.
 */
class IClonerScene implements ClonerScene
{

    protected $_root;

    protected $_env;

    public function __construct(Ucl $root, array $env)
    {
        $this->_root = $root;
        $this->_env = $env;
        SpyAgency::incr(static::class);
    }

    public function __get($name)
    {
        return $this->{"_$name"};
    }

    public function __destruct()
    {
        unset($this->_root);
        SpyAgency::decr(static::class);
    }
}