<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Ghost\Stdio\Context;

use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\ACodeContext;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HelloWorld extends ACodeContext
{
    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([]);
    }

    public function __on_start(StageBuilder $builder): StageBuilder
    {
        return $builder->onActivate(function(Dialog $dialog) : Operator {

            return $dialog
                ->send()
                ->info('hello world!')
                ->over()
                ->await();

        })->onReceive(function(Dialog $dialog) : Operator {
            return $dialog
                ->send()
                ->info('receive : ' . $dialog->message->getText())
                ->over()
                ->reactivate();
                //->fulfill();
        });
    }


}