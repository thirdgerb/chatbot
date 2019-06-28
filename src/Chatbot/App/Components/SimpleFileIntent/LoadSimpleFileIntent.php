<?php


namespace Commune\Chatbot\App\Components\SimpleFileIntent;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\NLU\NLUExample;
use Symfony\Component\Finder\Finder;

class LoadSimpleFileIntent extends ServiceProvider
{
    /**
     * @var string
     */
    protected $resourcePath;


    public function __construct($app, string $resourcePath)
    {
        $this->resourcePath = $resourcePath;
        parent::__construct($app);
    }

    public function boot($app)
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->resourcePath)
            ->name('/\.md$/');

        $repo = IntentRegistrar::getIns();

        foreach ($finder as $fileInfo) {
            $path = $fileInfo->getPathname();
            $name = str_replace($this->resourcePath, '', $path);
            $name = str_replace('.md', '', $name);
            $name = str_replace('/', '.', $name);
            $name = FileIntOption::PREFIX . '.' . $name;

            $option = new FileIntOption(
                $name,
                $path,
                $fileInfo->getContents()
            );

            $def = new SimpleFileIntDefinition($option);
            $repo->register($def);

            // 注册意图.
            $examples = $option->examples;
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