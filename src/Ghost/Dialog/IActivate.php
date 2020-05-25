<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IActivate extends AbsDialog implements Activate
{

    public function __construct(Dialog $prev, Ucl $ucl)
    {
        parent::__construct($prev->cloner, $ucl, $prev);
    }

}