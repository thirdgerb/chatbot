<?php


namespace Commune\Chatbot\App\Contexts\Restful;


use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Entity;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class PathEtt implements Entity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $realName;

    /**
     * @var string
     */
    protected $question ;

    /**
     * PathEtt constructor.
     * @param string $name
     * @param string $askForValue
     */
    public function __construct(string $name, string $askForValue = '请输入 %name% :')
    {
        $this->realName = $name;
        $this->name = 'pathValueOf'.ucfirst($name);
        $this->question = $askForValue;
    }

    protected function except(string $clazz)
    {
        throw new ConfigureException(
            static::class
            . 'is only available for ' . ResourceDef::class
            . ', not '.$clazz
        );
    }

    public function set(Context $self, $value): void
    {
        if (!$self instanceof ResourceDef) {
            $this->except(get_class($self));
        }
        $data = $self->paths ?? [];
        $data[$this->realName] = $value;
        $self->paths = $data;
    }

    public function get(Context $self)
    {
        if (!$self instanceof ResourceDef) {
            $this->except(get_class($self));
        }
        return $self->paths[$this->realName] ?? null;
    }

    public function isPrepared(Context $self): bool
    {
        $value = $this->get($self);
        return isset($value);
    }

    public function asStage(Stage $stageRoute): Navigator
    {
        return $stageRoute->buildTalk()
            ->withSlots(['name' => $this->realName])
            ->askVerbose($this->question)
            ->wait()
            ->hearing()
            ->isAnswer(function(ResourceDef $self, Dialog $dialog, Answer $answer){
                $result = $answer->toResult();
                if (!preg_match('/^\w+$/', $result)) {
                    $dialog->say()->error('必须是英文字符加数字,下划线');
                    return $dialog->repeat();
                }
                $this->set($self, $result);
                return $dialog->next();
            })
            ->end();
    }

    public function __get($name)
    {
        return $this->{$name};
    }


}