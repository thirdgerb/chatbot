<?php

namespace Commune\Chatbot\App\Components\SimpleFileIntent;

use Symfony\Component\Yaml\Yaml;

/**
 * @property-read string $name
 * @property-read string $desc
 * @property-read string $question
 * @property-read string[] $suggestions
 * @property-read string $content
 * @property-read string[] $examples
 */
class FileIntOption
{
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
     * @var string
     */
    protected $question;


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
     * FileIntOption constructor.
     * @param string $name
     * @param string $filePath
     * @param string $content
     */
    public function __construct(
        string $name,
        string $filePath,
        string $content
    )
    {
        $this->name = $name;
        $this->filePath = $filePath;
        $this->initialize($content);
    }

    protected function initialize(string $data) : void
    {
        $secs = explode("\n---\n", $data, 2);

        if (count($secs) > 1) {
            list($yamlStr, $content) = $secs;
            $yaml = Yaml::parse(trim($yamlStr));
        } else {
            $content = $data;
            $yaml = [];
        }


        $this->desc = trim($yaml['description'] ?? '');
        $this->suggestions = $yaml['suggestions'] ?? [];
        $this->content = trim($content);
        $this->question = trim($yaml['question'] ?? '请问还有想了解的吗?');
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