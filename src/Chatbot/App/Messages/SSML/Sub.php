<?php


namespace Commune\Chatbot\App\Messages\SSML;


use Commune\Chatbot\Framework\Messages\AbsSSML;

/**
 * SUB 标签
 */
class Sub extends AbsSSML
{
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


    public static function mock()
    {
        return new static('test', 'alias');
    }
}