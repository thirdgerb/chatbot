<?php


namespace Commune\Chatbot\App\Components;


use Commune\Chatbot\Framework\Component\ComponentOption;

/**
 * 系统预定义的组件. 提供demo样例, navigator等.
 */
class PredefinedIntComponent extends ComponentOption
{
    protected function doBootstrap(): void
    {
        $namespace = "Commune\\Chatbot\\App\\Components\\Predefined\\";
        $path = __DIR__ . '/Predefined/';
        $this->loadSelfRegisterByPsr4(
            $namespace,
            $path
        );
    }

    public static function stub(): array
    {
        return [];
    }


}