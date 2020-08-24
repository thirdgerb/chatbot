<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Rasa;

use Commune\Blueprint\NLU\NLUServiceOption;
use Commune\Framework\Component\AComponentOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $domainFilePath
 * @property-read string $nluFilePath
 * @property-read NLUServiceOption $nluOption
 *
 */
class RasaComponent extends AComponentOption
{


    public static function stub(): array
    {
        return [
            'domainFilePath' => __DIR__. '/rasa_demo/data/domain.yml',
            'nluFilePath' => __DIR__ . '/rasa_demo/data/nlu.yml',
            'nluOption' => RasaService::defaultOption(),
        ];
    }

    public static function relations(): array
    {
        return [
            'nluOption' => NLUServiceOption::class,
        ];
    }



}