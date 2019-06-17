<?php


namespace Commune\Chatbot\App\Messages\QA;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Messages\QA\AbsAnswer;

/**
 * Verbose Answer
 */
class VbAnswer extends AbsAnswer
{
    protected $answer;

    /**
     * VbAnswer constructor.
     * @param Message $origin
     * @param string $answer
     * @param null|int $choice
     */
    public function __construct(
        Message $origin,
        string $answer,
        $choice = null
    ) {
        $this->answer = $answer;
        parent::__construct($origin, $choice);
    }

    public function toResult()
    {
        return $this->answer;
    }

    public function namesAsDependency(): array
    {
        return array_merge(parent::namesAsDependency(), [VbAnswer::class]);
    }
}