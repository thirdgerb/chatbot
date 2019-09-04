<?php


namespace Commune\Chatbot\App\Messages\Templates;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Contracts\Translator;

class TranslateTemp implements ReplyTemplate
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * TranslateTemp constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }


    public function render(ReplyMsg $reply, Conversation $conversation): array
    {
        $id = $reply->getText();
        $slots = $this->dot($reply->getSlots());
        $text = $this->translator->trans($id, $slots);
        $message = (new Text($text))->withLevel($reply->getLevel());
        return [$message];
    }



    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array|\Traversable $slots
     * @param string $prefix
     * @return array
     */
    protected function dot($slots, string $prefix = '') : array
    {
        $result = [];
        $prefix = empty($prefix) ? '' : "$prefix.";

        foreach ($slots as $key => $value) {
            $key = strval($key);

            if (is_iterable($value)) {
                $result = $result + $this->dot($value, "$prefix$key");
            }

            if (is_scalar($value)) {
                $result["$prefix$key"] = $value;
            }

            // 其它类型数据跳过
            continue;
        }

        return $result;
    }


}