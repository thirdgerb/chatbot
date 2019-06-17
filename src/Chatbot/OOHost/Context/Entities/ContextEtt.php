<?php


namespace Commune\Chatbot\OOHost\Context\Entities;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Entity;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

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
     * @var Context
     */
    protected $to;

    /**
     * ContextEtt constructor.
     * @param string $name
     * @param Context $to
     */
    public function __construct(string $name, Context $to)
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
            clone $this->to,
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