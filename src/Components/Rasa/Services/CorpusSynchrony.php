<?php


namespace Commune\Components\Rasa\Services;


use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Chatbot\OOHost\NLU\Options\SynonymOption;
use Commune\Components\Rasa\RasaComponent;
use Symfony\Component\Yaml\Yaml;

class CorpusSynchrony
{
    /**
     * @var RasaComponent
     */
    protected $config;

    /**
     * @var Corpus
     */
    protected $corpus;

    /**
     * CorpusSynchrony constructor.
     * @param RasaComponent $config
     * @param Corpus $corpus
     */
    public function __construct(RasaComponent $config, Corpus $corpus)
    {
        $this->config = $config;
        $this->corpus = $corpus;
    }


    public function outputCorpus(): void
    {

        $this->renderNLUData();
        $this->renderDomain();
    }

    protected function renderNLUData()
    {
        $output = '';
        $output = $this->addIntentCorpus($output);
        $output = $this->addRegexToOutput($output);
        $output = $this->addSynonymToOutput($output);
        $output = $this->addLookUpToOutput($output);
        file_put_contents($this->config->output, $output);
    }

    protected function renderDomain()
    {
        $output = [ 'intents' => [] ];

        foreach ($this->corpus->intentCorpusManager()->each() as $option) {
            /**
             * @var IntentCorpusOption $option
             */
            $output['intents'][] = $option->name;
        }
        $str = Yaml::dump($output, 3);
        file_put_contents($this->config->domainOutput, $str);
    }


    protected function addIntentCorpus(string $output) : string
    {
        foreach ($this->corpus->intentCorpusManager()->each() as $intentCorpus) {
            /**
             * @var IntentCorpusOption $intentCorpus
             */
            $examples = $intentCorpus->examples;
            if (empty($examples)) {
                continue;
            }

            $name = $intentCorpus->name;

            $output .= "\n## intent:$name \n";
            foreach ($examples as $example) {
                // NLUExample 采用和rasa 一致的标注格式.
                $output .= '- ' . $example . PHP_EOL;
            }
        }
        return $output;
    }


    protected function addSynonymToOutput(string $output) : string
    {
        foreach ($this->corpus->synonymsManager()->each() as $option) {
            /**
             * @var SynonymOption $option
             */
            $name = $option->value;
            $words = $option->aliases;
            if (empty($words)) {
                continue;
            }
            $output .= "\n## synonym:$name\n";
            $words = implode(' ' , $words);
            $output .= "- $words\n";
        }

        return $output;
    }

    protected function addRegexToOutput(string $output) : string
    {
        return $output;
    }

    protected function addLookUpToOutput(string $output) : string
    {
        foreach ($this->corpus->entityDictManager()->each() as $option) {
            /**
             * @var EntityDictOption $option
             */
            $name = $option->name;
            $values = $option->values;
            $output .= "\n## lookup:$name\n";
            foreach ($values as $item) {
                $output .= "- $item\n";
            }
        }

        return $output;
    }


}