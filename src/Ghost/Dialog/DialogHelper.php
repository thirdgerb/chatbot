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

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialogue\Withdraw;
use Commune\Blueprint\Ghost\Dialogue\Retain;
use Commune\Blueprint\Ghost\Dialogue\Activate;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DialogHelper
{

    /*-------- redirect --------*/

    public static function onRedirect(Dialog $from, Dialog $to) : ? Dialog
    {
        $stageDef = $to->ucl->findStageDef($to->cloner);
        return $stageDef->onRedirect($from, $to);
    }

    public static function onEscaper(Retrace $escaper) : ? Dialog
    {
        $stageDef = $escaper->ucl->findStageDef($escaper->cloner);
        return $stageDef->onWithdraw($escaper);
    }

    public static function onActivate(OnActivate $activate) : Dialog
    {
        $stageDef = $activate->ucl->findStageDef($activate->cloner);
        return $stageDef->onActivate($activate);
    }

    public static function onRetain(Retain $retain) : Dialog
    {
        $stageDef = $retain->ucl->findStageDef($retain->cloner);
        return $stageDef->onReceive($retain);
    }


}