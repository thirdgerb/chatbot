<?php


namespace Commune\Chatbot\OOHost\NLU\Providers;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\NLU\Corpus\CorpusRepository;
use Commune\Chatbot\OOHost\NLU\NLUComponent;
use Commune\Container\ContainerContract;
use Commune\Support\OptionRepo\Contracts\OptionRepository;


/**
 * nlu 相关组件的注册服务.
 */
class NLUServiceProvider extends BaseServiceProvider
{
    // nlu 通常有IO 开销, 应该放到 conversation 级别.
    const IS_PROCESS_SERVICE_PROVIDER = false;

    /**
     * @var NLUComponent
     */
    protected $component;

    /**
     * NLUServiceProvider constructor.
     * @param ContainerContract $app
     * @param NLUComponent $component
     */
    public function __construct(ContainerContract $app, NLUComponent $component)
    {
        $this->component = $component;
        parent::__construct($app);
    }


    public function boot($app)
    {
    }

    public function register()
    {
        $this->registerCorpus();
        $this->registerNLUService();
        $this->registerNLULogger();

    }

    protected function registerCorpus() : void
    {
        // 注册 copus
        if (!$this->app->bound(Corpus::class)) {
            $this->app->singleton(Corpus::class, function($app){
                return new CorpusRepository(
                    $app[IntentRegistrar::class],
                    $app[OptionRepository::class],
                    $app
                );
            });
        }
    }

    protected function registerNLUService() : void
    {
        if (!$this->app->bound(NLUService::class)) {
            $this->app->singleton(NLUService::class, $this->component->nluService);
        }
    }

    protected function registerNLULogger() : void
    {
        if (!$this->app->bound(NLULogger::class)) {
            $this->app->singleton(NLULogger::class, $this->component->nluLogger);
        }
    }


}