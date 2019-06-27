<?php


namespace Commune\Chatbot\App\Components;


use Commune\Chatbot\Framework\Component\ComponentOption;


/**
 * @property-read string $namespace
 * @property-read string $path
 */
class SelfRegisterComponent extends ComponentOption
{
    public static function stub(): array
    {
        return [
            'namespace' => '', //"Commune\\Demo\\App",
            'path' => '', //__DIR__
        ];
    }

    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4($this->namespace, $this->path);
    }

}