<?php


namespace Commune\Demo\App\Components\CustomService\Tasks;

use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class RandomTest extends TaskDef
{

    const DESCRIPTION = '测试intent和task混合';

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->talk(function(Dialog $dialog) {
            $dialog->say()->info('随便测试一下');
            return $dialog->fulfill();
        });
    }

    public function __exiting(Exiting $listener): void
    {
    }


}