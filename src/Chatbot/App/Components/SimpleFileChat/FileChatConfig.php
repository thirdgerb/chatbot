<?php

namespace Commune\Chatbot\App\Components\SimpleFileChat;

use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Symfony\Component\Yaml\Yaml;

/**
 * @property-read string $name
 * @property-read string $desc
 * @property-read string[] $suggestions
 * @property-read string[] $contents
 * @property-read string[] $examples
 * @property-read GroupOption $groupOption
 */
class FileChatConfig
{
    // simple file intent
    const PREFIX = 'sfi';

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $desc;

    /**
     * @var string[]
     */
    protected $suggestions;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string[]
     */
    protected $examples;

    /**
     * @var GroupOption
     */
    protected $groupOption;

    /**
     * FileIntConfig constructor.
     * @param string $name
     * @param string $filePath
     * @param string $content
     * @param GroupOption $option
     */
    public function __construct(
        string $name,
        string $filePath,
        string $content,
        GroupOption $option
    )
    {
        $this->name = $name;
        $this->filePath = $filePath;
        $this->groupOption = $option;
        $this->initialize($content);
    }

    protected function initialize(string $data) : void
    {
        $secs = explode("\n---\n", $data);

        if (count($secs) > 1) {
            $yamlStr = array_shift($secs);
            $content = $secs;
            $yaml = Yaml::parse(trim($yamlStr));
        } else {
            throw new ConfigureException(
                __METHOD__
                . ' simple file chat config file must separate context by "\n---\n"'
            );

        }


        $this->desc = trim($yaml['description'] ?? '');
        $this->suggestions = $yaml['suggestions'] ?? [];
        $this->contents = $content;
        $this->examples = $yaml['examples'] ?? [];
    }



    public function __get($name)
    {
        return $this->{$name};
    }

    public function __isset($name)
    {
        return property_exists($this, $name);
    }
}