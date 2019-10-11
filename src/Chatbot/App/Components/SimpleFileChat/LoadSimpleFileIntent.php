<?php


namespace Commune\Chatbot\App\Components\SimpleFileChat;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrarImpl;
use Commune\Chatbot\OOHost\NLU\Corpus\IntExample as NLUExample;
use Symfony\Component\Finder\Finder;

class LoadSimpleFileIntent extends ServiceProvider
{
    /**
     * @var string
     */
    protected $resourcePath;

    /**
     * @var GroupOption
     */
    protected $option;

    public function __construct($app, GroupOption $option)
    {
        $this->resourcePath = $option->resourcePath;
        $this->option = $option;
        parent::__construct($app);
    }

    public function boot($app)
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->resourcePath)
            ->name('/\.md$/');

        $repo = $app->get(IntentRegistrar::class);
        $id = $this->option->id;

        foreach ($finder as $fileInfo) {
            $path = $fileInfo->getPathname();
            $name = str_replace($this->resourcePath, '', $path);
            $name = str_replace('.md', '', $name);
            $name = trim($name, '/');
            $name = str_replace('/', '.', $name);
            $name = FileChatConfig::PREFIX . ".$id." . $name;

            $configs = new FileChatConfig(
                $name,
                $path,
                $fileInfo->getContents(),
                $this->option
            );

            $def = new SimpleFileIntDefinition($configs);
            $repo->registerDef($def);


            // 注册意图.
            $examples = $configs->examples;
            if (!empty($examples)) {
                foreach ($examples as $example) {
                    $repo->registerNLUExample($name, new NLUExample($example));
                }
            }
        }
    }

    public function register()
    {
    }


}