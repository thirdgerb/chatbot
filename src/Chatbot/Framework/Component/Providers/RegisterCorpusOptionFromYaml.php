<?php


namespace Commune\Chatbot\Framework\Component\Providers;

use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Container\ContainerContract;
use Commune\Support\Option;
use Symfony\Component\Yaml\Yaml;


/**
 * 从 yaml 中读取 option, 并加载到语料库 corpus中, 作为语料库的补充.
 */
class RegisterCorpusOptionFromYaml extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;


    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $optionClazz;

    /**
     * @var bool
     */
    protected $force;


    /**
     * @var bool
     */
    protected $sync;

    /**
     * RegisterCorpusOptionFromYaml constructor.
     * @param ContainerContract $app
     * @param string $resource
     * @param string $optionClazz
     * @param bool $force  如果 force = false, 只有在同名option 不存在时才会加载.
     * @param bool $sync 是否将结果保存到 option repository
     */
    public function __construct(
        ContainerContract $app,
        string $resource,
        string $optionClazz,
        bool $force = false,
        bool $sync = false
    )
    {
        $this->resource = $resource;
        $this->optionClazz = $optionClazz;
        $this->force = $force;
        parent::__construct($app);
    }


    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
        $resource = $this->resource;
        if (!file_exists($resource)) {
            throw new ChatbotLogicException(
                __METHOD__
                . $this->optionClazz
                . ' resource '
                . $resource
                . ' not exists, json file expected'
            );
        }

        $data = file_get_contents($resource);
        $options = Yaml::parse($data);

        if (empty($options)) {
            return;
        }

        if (!is_array($options)) {
            throw new ChatbotLogicException(
                __METHOD__
                .' nlu examples resource '
                . $resource
                . ' invalid, json expected'
            );
        }

        /**
         * @var Corpus $corpus
         */
        $corpus = $app[Corpus::class];
        $manager = $corpus->getManager($this->optionClazz);
        if (empty($manager)) {
            throw new ChatbotLogicException(
                __METHOD__
                . ' corpus manager for '
                . $this->optionClazz
                . ' not exists'
            );
        }

        /**
         * @var ConsoleLogger $logger
         */
        $logger = $app[ConsoleLogger::class];

        $clazz = $this->optionClazz;
        $registered = false;
        foreach ($options as $example) {
            /**
             * @var Option $option
             */
            $option = new $clazz($example);
            $id = $option->getId();
            if (empty($id)) {
                continue;
            }

            if ($this->force || ! $manager->hasSynced($id)) {
                $registered = $manager->register($option);
            }
        }

        if ($registered) {
            $logger->info("register corpus option $clazz from $resource to corpus");
        }

        // 通常不 sync
        if ($registered && $this->sync) {
            $logger->info("sync corpus option after register $clazz from $resource");
            $corpus->sync(false);
        }
    }

    public function register()
    {
    }


}