<?php


namespace Commune\Chatbot\App\Messages\SSML;


use Commune\Chatbot\Framework\Messages\AbsSSML;

class Sub extends AbsSSML
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * Sub constructor.
     * @param string $content
     * @param string $alias
     */
    public function __construct(string $content, string $alias)
    {
        parent::__construct($content, ['alias' => $alias]);
    }

    public function getTag(): string
    {
        return 'sub';
    }


}