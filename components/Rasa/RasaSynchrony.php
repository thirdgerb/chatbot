<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Rasa;

use Commune\Blueprint\Ghost\MindDef\EntityDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\SynonymDef;
use Commune\Blueprint\Ghost\Mindset;
use Symfony\Component\Yaml\Yaml;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RasaSynchrony
{

    /**
     * @var RasaComponent
     */
    protected $config;

    /**
     * @var Mindset
     */
    protected $mind;

    protected $intentNames = [];

    public function __construct(RasaComponent $config, Mindset $mind)
    {
        $this->config = $config;
        $this->mind = $mind;
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
        $output = $this->addSynonymToOutput($output);
        $output = $this->addLookUpToOutput($output);
        file_put_contents($this->config->nluFilePath, $output);
    }

    protected function renderDomain()
    {
        $output = [ 'intents' => $this->intentNames ];
        $str = Yaml::dump($output, 3);
        file_put_contents($this->config->domainFilePath, $str);
    }


    protected function addIntentCorpus(string $output) : string
    {
        foreach ($this->mind->intentReg()->each() as $intentDef) {

            $name = $intentDef->getName();
            $this->intentNames[] = $name;
            /**
             * @var IntentDef $intentDef
             */
            $examples = $intentDef->getExamples();
            if (empty($examples)) {
                continue;
            }


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
        foreach ($this->mind->synonymReg()->each() as $option) {
            /**
             * @var SynonymDef $option
             */
            $name = $option->getName();
            $words = $option->getValues();
            if (empty($words)) {
                continue;
            }
            $output .= "\n## synonym:$name\n";
            $words = implode(' ' , $words);
            $output .= "- $words\n";
        }

        return $output;
    }

    protected function addLookUpToOutput(string $output) : string
    {
        foreach ($this->mind->entityReg()->each() as $option) {
            /**
             * @var EntityDef $option
             */
            $name = $option->getName();
            $values = $option->getValues();
            $output .= "\n## lookup:$name\n";
            foreach ($values as $item) {
                $output .= "- $item\n";
            }
        }

        return $output;
    }


}