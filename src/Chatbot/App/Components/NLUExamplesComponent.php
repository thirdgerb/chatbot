<?php


namespace Commune\Chatbot\App\Components;


use Commune\Chatbot\Framework\Component\ComponentOption;

/**
 * 用于管理意图例句的组件.
 * 对话中输入 /redirect nlu.examples.manager
 * 可以进入到相关语境, 编辑意图的例句, 并保存到一个json文件中.
 * 为NLU单元提供准备.
 *
 * @property-read string $repository  存放 intent 例句的文件.
 */
class NLUExamplesComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'repository' => __DIR__ .'/NLUExamples/repository.json',
        ];
    }



    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Chatbot\\App\\Components\\NLUExamples\\",
            __DIR__ .'/NLUExamples/'
        );

        $this->loadIntentCorpusFromYaml($this->repository);
    }


}