<?php


namespace Commune\Chatbot\App\Messages\QA;

use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\Framework\Messages\QA\AbsAnswer;
use Commune\Chatbot\Framework\Messages\Traits\VerbalTrait;

/**
 * Verbose Answer
 * 用户回答的文字结果. 默认类型是字符串. 需要自己做类型转换.
 */
class VbAnswer extends AbsAnswer implements VerbalMsg
{
    use VerbalTrait;

    protected $answer;

    /**
     * VbAnswer constructor.
     * @param Message $origin
     * @param mixed $answer
     * @param null|int|string $choice
     */
    public function __construct(
        Message $origin,
        string $answer,
        $choice = null
    ) {
        $this->answer = $answer;
        parent::__construct($origin, $choice);
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['answer']);
    }

    /**
     * @return string
     */
    public function toResult()
    {
        return $this->answer;
    }

    public static function mock()
    {
        return new VbAnswer(new Text('a'), 'abc', 1);
    }
}