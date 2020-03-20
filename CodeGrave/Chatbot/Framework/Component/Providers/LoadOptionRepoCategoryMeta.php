<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Support\OptionRepo\Options\CategoryMeta;

class LoadOptionRepoCategoryMeta extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @var CategoryMeta
     */
    protected $meta;

    /**
     * LoadOptionRepoCategoryMeta constructor.
     * @param ContainerContract $app
     * @param CategoryMeta $meta
     */
    public function __construct(ContainerContract $app, CategoryMeta $meta)
    {
        $this->meta = $meta;
        parent::__construct($app);
    }


    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
        /**
         * @var OptionRepository $repo
         */
        $repo = $app->get(OptionRepository::class);
        $repo->registerCategory($this->meta);
    }

    public function register()
    {
    }


}