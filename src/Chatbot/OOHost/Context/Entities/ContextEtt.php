<?php


namespace Commune\Chatbot\OOHost\Context\Entities;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Entity;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 用一个 context 作为当前 context 的 entity
 */
class ContextEtt implements Entity
{

    /**
     * @var string
     */
    protected $name ;

    /**
     * @var string
     */
    protected $desc = '';

    /**
     * @var string
     */
    protected $to;

    protected $question = '';

    /**
     * ContextEtt constructor.
     * @param string $name
     * @param string $to
     */
    public function __construct(string $name,string $to)
    {
        $this->name = $name;
        $this->to = $to;
    }

    public function set(Context $self, $value): void
    {
        if ($this->isValidValue($value)) {
            $self->setAttribute($this->name, $value);
        }
    }

    public function get(Context $self)
    {
        return $self->getAttribute($this->name);
    }

    public function isPrepared(Context $self): bool
    {
        $value = $self->getAttribute($this->name);
        return $this->isValidValue($value);
    }

    protected function isValidValue($value) : bool
    {
        return $value instanceof Context
            && is_a($value, get_class($this->to), TRUE)
            && $value->isPrepared();
    }

    public function asStage(Stage $stageRoute): Navigator
    {
        return $stageRoute->dependOn(
            $this->to,
            function (Context $self, Dialog $dialog, Context $message) {
                $self->setAttribute($this->name, $message);
                return $dialog->next();
            }
        );
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}