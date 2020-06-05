<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Contexts;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Context\ACodeContext;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @desc demo.contexts.featureTest
 */
class FeatureTest extends ACodeContext
{
    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'onCancel' => 'cancel',
            'onQuit' => 'quit',
            'stageRoutes' => [
                'quit',
                'rewind',
            ],
        ]);
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage->always(function(Dialog $dialog){
            return $dialog
                ->send()
                ->info('hello world')
                ->over()
                ->await();
        });
    }

    /**
     * @param StageBuilder $builder
     * @return StageBuilder
     *
     * @title #q
     */
    public function __on_quit(StageBuilder $builder): StageBuilder
    {
        return $builder->always(function(Dialog $dialog) {
            return $dialog->send()
                ->info('quit from event')
                ->over()
                ->quit();
        });
    }

    /**
     * @param StageBuilder $builder
     * @return StageBuilder
     *
     * @title #r
     */
    public function __on_rewind(StageBuilder $builder) : StageBuilder
    {
        return $builder->onRedirect(function(Dialog $prev) {
            return $prev->send()->info('rewind')->over()->reactivate();
        });
    }

    public function __on_cancel(StageBuilder $builder): StageBuilder
    {
        return $builder->always(function(Dialog $dialog) {
            return $dialog->send()
                ->info('cancel from event')
                ->over()
                ->cancel();
        });
    }

}