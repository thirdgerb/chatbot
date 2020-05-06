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
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IActivate;
use Commune\Ghost\Dialog\IFinale;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DialogHelper
{
    const IMPLEMENTS = [
        Receive\Heed::class => '',
        Activate\Staging::class => IActivate\IStaging::class,
        Activate\Intend::class => IActivate\IIntend::class,
        Dialog\Finale\Dumb::class => IFinale\IDumb::class,

    ];

    public static function newDialog(Dialog $prev, Ucl $ucl, string $type) : Dialog
    {
        $implements = self::IMPLEMENTS[$type];

        /**
         * @var AbsDialogue $newDialog
         */
        $newDialog = new $implements($prev->cloner, $ucl);
        $newDialog->withPrev($prev);
        return $newDialog;
    }


    public static function intercept(Dialog $dialog) : ? Dialog
    {
        $stageDef = $dialog->ucl->findStageDef($dialog->cloner);
        return $stageDef->onIntercept($dialog, $dialog->prev);
    }

    public static function activate(Activate $dialog, Ucl $ucl) : Dialog
    {
        $stageDef = $ucl->findStageDef($dialog->cloner);
        return $stageDef->onActivate($dialog);
    }
}