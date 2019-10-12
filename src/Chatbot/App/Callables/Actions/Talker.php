<?php


namespace Commune\Chatbot\App\Callables\Actions;


use Commune\Chatbot\OOHost\Dialogue\Dialog;

/**
 * 为了省去写闭包, 而用链式调用生成一个 callable, 所以发明了 talk 类.
 * 未来还可以设计更多这样的builder
 */
class Talker
{
    /**
     * @var array
     */
    protected $slots;

    /**
     * @var bool
     */
    protected $paragraph;

    /**
     * @var array
     */
    protected $talking = [];

    /**
     * Talk constructor.
     * @param array $slots
     * @param bool $paragraph
     */
    public function __construct(array $slots, bool $paragraph)
    {
        $this->slots = $slots;
        $this->paragraph = $paragraph;
    }

    public static function say(array $slots = [], bool $paragraph = false) : Talker
    {
        return new static($slots, $paragraph);
    }


    public function __invoke(Dialog $dialog)
    {
        $speech = $dialog->say($this->slots);
        if ($this->paragraph) $speech->beginParagraph();

        foreach ($this->talking as list ($level, $message, $context)) {
            $speech->{$level}($message, $context);
        }

        if ($this->paragraph) $speech->endParagraph();

        return null;
    }

    public function debug(string $message, array $slots = []) : Talker
    {
        $this->talking[] = [__FUNCTION__, $message, $slots];
        return $this;
    }

    public function info(string $message, array $slots = []) : Talker
    {
        $this->talking[] = [__FUNCTION__, $message, $slots];
        return $this;
    }

    public function warning(string $message, array $slots = []) : Talker
    {
        $this->talking[] = [__FUNCTION__, $message, $slots];
        return $this;
    }

    public function notice(string $message, array $slots = []) : Talker
    {
        $this->talking[] = [__FUNCTION__, $message, $slots];
        return $this;
    }

    public function error(string $message, array $slots = [])  : Talker
    {
        $this->talking[] = [__FUNCTION__, $message, $slots];
        return $this;
    }


}