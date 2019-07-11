<?php


namespace Commune\Chatbot\App\Components\SimpleChat;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Symfony\Component\Finder\Finder;

/**
 * service provider
 */
class LoadSimpleChat extends ServiceProvider
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
            ->name('*.json');

        foreach ($finder as $fileInfo) {

            $path = $fileInfo->getPathname();
            $name = str_replace($this->resourcePath, '', $path);
            $name = str_replace('.json', '', $name);
            $name = str_replace('/', '.', $name);
            $index = trim($name, '.');
            Manager::loadResource($index, $path);
        }
    }

    public function register()
    {
    }


}