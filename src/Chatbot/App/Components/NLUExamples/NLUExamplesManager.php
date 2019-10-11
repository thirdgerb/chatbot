<?php


namespace Commune\Chatbot\App\Components\NLUExamples;


use Commune\Chatbot\App\Components\NLUExamplesComponent;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\NLU\Corpus\IntExample as NLUExample;

class NLUExamplesManager
{
    /**
     * @var NLUExamplesComponent
     */
    protected $config;

    /**
     * @var IntentRegistrar
     */
    protected $repo;

    /**
     * NLUExamplesManager constructor.
     * @param NLUExamplesComponent $config
     * @param IntentRegistrar $repo
     */
    public function __construct(NLUExamplesComponent $config, IntentRegistrar $repo)
    {
        $this->config = $config;
        $this->repo = $repo;
    }


    public function register(string $intentName, NLUExample $example) : void
    {
        $repo = $this->repo;
        $repo->registerNLUExample($intentName, $example);
        $this->generate();
    }

    /**
     * 生成 NLU example 的 repository 文件.
     */
    public function generate() : void
    {
        $registrar = $this->repo;
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