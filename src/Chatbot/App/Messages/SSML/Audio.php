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

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['source']);
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    public function getTag(): string
    {
        return 'audio';
    }

    public static function mock()
    {
        return new static('mock audio');
    }


}
