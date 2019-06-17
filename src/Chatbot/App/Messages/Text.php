<?php


namespace Commune\Chatbot\App\Messages;


use Commune\Chatbot\Framework\Messages\Verbose;

/**
 * 默认的文本消息.
 * @method Text withSlots(array $slots)
 * @method Text raw()
 * @method Text withLevel(string $level)
 */
class Text extends Verbose
{

    /**
     * Text constructor.
     * @param string $input
     */
    public function __construct(string $input)
    {
        parent::__construct($input);
    }
}