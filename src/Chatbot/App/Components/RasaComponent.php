<?php


namespace Commune\Chatbot\App\Components;


use Commune\Chatbot\App\Components\Rasa\Options\LookupOption;
use Commune\Chatbot\App\Components\Rasa\Options\RegexOption;
use Commune\Chatbot\App\Components\Rasa\Options\SynonymOption;
use Commune\Chatbot\App\Components\Rasa\RasaNLUPipeImpl;
use Commune\Chatbot\App\Components\Rasa\RasaServiceProvider;
use Commune\Chatbot\Framework\Component\ComponentOption;

/**
 * @property-read string $server
 * @property-read string $output
 * @property-read string $jwt
 * @property-read int $threshold  阈值
 * @property-read string|\Closure $rasaPipe
 * @property-read SynonymOption[] $synonym
 * @property-read LookupOption[] $lookup
 * @property-read RegexOption[] $regex
 *
 */
class RasaComponent extends ComponentOption
{
    protected static $associations =[
        'synonym[]' => SynonymOption::class,
        'lookup[]' => LookupOption::class,
        'regex[]' => RegexOption::class
    ];


    public static function stub(): array
    {
        return [
            'server' => 'localhost:5005',
            'jwt' => '',
            'rasaPipe' => RasaNLUPipeImpl::class,
            'threshold' => 70,
            'output' => __DIR__ .'/Rasa/nlu.md',
            'synonym' => [
                //SynonymOption::stub(),
            ],
            'lookup' => [
                //LookupOption::stub(),
            ],
            'regex' => [
                //RegexOption::stub(),
            ]
        ];
    }

    protected function doBootstrap(): void
    {
        $this->app->registerConversationService(
            new RasaServiceProvider(
                $this->app->getConversationContainer(),
                $this->rasaPipe
            )
        );

        $this->loadSelfRegisterByPsr4(
            "Commune\\Chatbot\\App\\Components\\Rasa\\Contexts\\",
            __DIR__ .'/Rasa/Contexts/'
        );
    }


}