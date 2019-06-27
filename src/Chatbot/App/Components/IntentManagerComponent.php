<?php


namespace Commune\Chatbot\App\Components;


use Commune\Chatbot\Framework\Component\ComponentOption;

/**
 * @property-read string $repository  存放 intent 例句的文件.
 */
class IntentManagerComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'repository' => __DIR__ .'/IntentManager/repository.json',
        ];
    }



    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Chatbot\\App\\Components\\IntentManager\\",
            __DIR__ .'/IntentManager/'
        );

        $this->loadNLUExampleFromJsonFile($this->repository);
    }


}