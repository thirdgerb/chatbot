<?php


namespace Commune\Chatbot\App\Messages;


use Commune\Chatbot\Framework\Messages\AbsVerbal;

/**
 * 默认的文本消息.
 */
class Text extends AbsVerbal
{
    /**
     * Text constructor.
     * @param string $input
     */
    public function __construct(string $input)
    {
        parent::__construct($input);
    }

    public static function mock()
    {
        return new static('test');
    }


}