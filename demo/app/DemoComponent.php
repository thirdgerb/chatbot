<?php


namespace Commune\Demo\App;


use Commune\Chatbot\App\Components\SelfRegisterComponent;

class DemoComponent extends SelfRegisterComponent
{

    public static function stub(): array
    {
        return [
            'namespace' => "Commune\\Demo\\App",
            'path' => __DIR__
        ];
    }

}