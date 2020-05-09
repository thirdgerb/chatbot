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
use Commune\Blueprint\Ghost\Dialog\Retain;
use Commune\Blueprint\Ghost\Dialog\Withdraw;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IActivate;
use Commune\Ghost\Dialog\IFinale;
use Commune\Ghost\Dialog\IRetain;
use Commune\Ghost\Dialog\IWithdraw;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DialogHelper
{
    const IMPLEMENTS = [
        Activate\Intend::class => IActivate\IRedirectTo::class,
        Activate\StartSession::class => IActivate\IStartSession::class,
        Retain\Heed::class => IRetain\IHeed::class,
        Retain\Preempt::class => IRetain\IPreempt::class,
        Retain\Fallback::class => IRetain\IFallback::class,
        Retain\Wake::class => IRetain\IWake::class,
        Retain\Restore::class => IRetain\IRestore::class,
        Retain\Watch::class => '',
        Withdraw\Reject::class => IWithdraw\IReject::class,
        Withdraw\Quit::class => IWithdraw\IQuit::class,
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

    public static function retain(Retain $dialog) : Dialog
    {
        $ucl = $dialog->ucl;
        $stageDef = $ucl->findStageDef($dialog->cloner);
        return $stageDef->onRetain($dialog);
    }
}