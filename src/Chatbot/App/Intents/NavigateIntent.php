<?php


namespace Commune\Chatbot\App\Intents;


use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\IntentMatcherOption;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 导航类Intent.
 * 核心是navigate方法, 可以预定义如何操作语境变化.
 */
abstract class NavigateIntent extends AbsCmdIntent
{
    /**
     * @var string 简介.
     */
    const DESCRIPTION = 'should define intent description by constant';

    const SIGNATURE = ''; // must be set
    const REGEX = [];
    const KEYWORDS = [];
    const EXAMPLES = [];

    public function __onStart(Stage $stageRoute): Navigator
    {
        return $stageRoute->dialog->fulfill();
    }


    public static function getMatcherOption(): IntentMatcherOption
    {
        if (empty(static::SIGNATURE)) {
            throw new ConfigureException(
                __METHOD__
                . ' need signature to define entities,'
                . ' empty value given'
            );
        }

        return new IntentMatcherOption([
            'signature' => static::SIGNATURE,
            'regex' => static::REGEX,
            'keywords' => static::KEYWORDS,
        ]);
    }

    abstract public function navigate(Dialog $dialog): ? Navigator;

    public function __exiting(Exiting $listener): void
    {
    }



}