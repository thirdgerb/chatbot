<?php


namespace Commune\Chatbot\App\Messages\SSML;


use Commune\Chatbot\Framework\Messages\AbsSSML;

class Audio extends AbsSSML
{
    /**
     * @var string
     */
    protected $source;

    /**
     * Audio constructor.
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->source = $source;
        parent::__construct('', ['src' => $source,]);
    }


    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }


}
