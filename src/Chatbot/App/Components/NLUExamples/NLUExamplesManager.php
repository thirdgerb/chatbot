<?php


namespace Commune\Chatbot\App\Components\NLUExamples;


use Commune\Chatbot\App\Components\NLUExamplesComponent;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\NLU\NLUExample;

class NLUExamplesManager
{
    /**
     * @var NLUExamplesComponent
     */
    protected $config;

    /**
     * NLUExamplesManager constructor.
     * @param NLUExamplesComponent $config
     */
    public function __construct(NLUExamplesComponent $config)
    {
        $this->config = $config;
    }

    public function register(string $intentName, NLUExample $example) : void
    {
        $repo = IntentRegistrar::getIns();
        $repo->registerNLUExample($intentName, $example);
        $this->generate();
    }

    /**
     * 生成 NLU example 的 repository 文件.
     */
    public function generate() : void
    {
        $registrar = IntentRegistrar::getIns();
        $all = $registrar->getNLUExamplesCollection();

        $data = [];
        foreach ($all as $intentName => $examples) {
            foreach ($examples as $example) {
                /**
                 * @var NLUExample $example
                 */
                $data[$intentName][] = $example->originText;
            }
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($this->config->repository, $json);
    }

}