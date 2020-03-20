<?php


namespace Commune\Components\Story\Providers;


use Commune\Container\ContainerContract;
use Commune\Chatbot\Blueprint\Application;
use Commune\Components\Story\StoryComponent;
use Commune\Components\Story\Basic\StoryRegistrar;
use Commune\Components\Story\Options\ScriptOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\Contracts\RootContextRegistrar;
use Commune\Components\Story\Basic\StoryRegistrarImpl;

class StoryServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @var StoryComponent
     */
    protected $storyOption;

    /**
     * StoryServiceProvider constructor.
     * @param ContainerContract $app
     * @param StoryComponent $storyOption
     */
    public function __construct(ContainerContract $app, StoryComponent $storyOption)
    {
        $this->storyOption = $storyOption;
        parent::__construct($app);
    }


    public function boot($app)
    {
        /**
         * @var StoryRegistrar $registrar
         * @var OptionRepository $repo
         * @var RootContextRegistrar $root
         */
        $root = $app[RootContextRegistrar::class];
        $root->registerSubRegistrar(StoryRegistrar::class);

        $registrar = $app[StoryRegistrar::class];
        $repo = $app[OptionRepository::class];
        foreach ($repo->eachOption(ScriptOption::class) as $script) {
            $registrar->registerScriptOption($script);
        }
    }

    public function register()
    {
        // 注册 story 服务.
        $this->app->singleton(StoryRegistrar::class, function($app){
            $chatApp = $app[Application::class];
            return new StoryRegistrarImpl($chatApp);
        });
    }


}