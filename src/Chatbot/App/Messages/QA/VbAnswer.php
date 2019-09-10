<?php


namespace Commune\Chatbot\App\Messages\QA;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Messages\QA\AbsAnswer;
use Commune\Chatbot\Framework\Messages\Traits\Verbosely;

/**
 * Verbose Answer
 * 用户回答的文字结果. 默认类型是字符串. 需要自己做类型转换.
 */
class VbAnswer extends AbsAnswer implements VerboseMsg
{
    use Verbosely;

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

    /**
     * @return string
     */
    public function toResult()
    {
        return $this->answer;
    }

    public function namesAsDependency(): array
    {
        return array_merge(parent::namesAsDependency(), [VbAnswer::class, VerboseMsg::class]);
    }



}