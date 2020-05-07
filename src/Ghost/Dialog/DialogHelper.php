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
use Commune\Blueprint\Ghost\Dialog\Withdraw;
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
        Receive\Preempt::class => '',
        Activate\Fallback::class => '',
        Activate\StartSession::class => '',
        Dialog\Finale\Dumb::class => IFinale\IDumb::class,
        Dialog\Finale\CloseSession::class => '',
    ];

    public static function newDialog(Dialog $prev, Ucl $ucl, string $type) : Dialog
    {
        $implements = self::IMPLEMENTS[$type];

        /**
         * @var AbsDialogue $newDialog
         */
        $newDialog = new $implements($prev->cloner, $ucl);
        return $newDialog;
    }


    public static function withdraw(Withdraw $dialog) : ? Dialog
    {
        $stageDef = $dialog->ucl->findStageDef($dialog->cloner);
        return $stageDef->onWithdraw($dialog);
    }


    public static function intercept(Activate $dialog) : ? Dialog
    {
        $stageDef = $dialog->ucl->findStageDef($dialog->cloner);
        return $stageDef->onIntercept($dialog, $dialog->prev);
    }

    public static function activate(Activate $dialog) : Dialog
    {
        $ucl = $dialog->ucl;
        $stageDef = $ucl->findStageDef($dialog->cloner);
        return $stageDef->onActivate($dialog);
    }
}