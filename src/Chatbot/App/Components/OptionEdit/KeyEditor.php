<?php


namespace Commune\Chatbot\App\Components\OptionEdit;


use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Support\Option;

/**
 * @property-read Option $option
 * @property-read string $key
 */
class KeyEditor extends TaskDef
{
    const DESCRIPTION = '修改option的一个值';

    const PRETTY = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;

    public function __construct(Option $option, string $key)
    {
        parent::__construct(get_defined_vars());
    }

    public function __onStart(Stage $stage): Navigator
    {
        $value = $this->option->{$this->key};

        return $stage->buildTalk()
            ->info(
                '正在修改' . $this->key
                . "\n 当前值为 :\n ".json_encode($value, self::PRETTY)
            )->askVerbose('请输入修改结果:')
            ->wait()
            ->hearing()
                ->isAnswer(function(Dialog $dialog, Answer $answer){
                    $result = $answer->toResult();
                    $dialog->say()->info("修改结果为: $result");
                    $this->setOptionValue($result);
                    return $dialog->fulfill();
                })
            ->end();
    }

    protected function setOptionValue($value)
    {
        $data = $this->option->toArray();
        list($name, $type, $desc) = $this->option->getProperties()[$this->key];

        switch($type) {
            case 'int' :
                $value = intval($value);
                break;
            case 'string' :
                $value = (string)$value;
                break;
            case 'float' :
                $value = floatval($value);
                break;
            case 'bool' :
                $value = boolval($value);
                break;
        }

        $data[$this->key] = $value;
        $this->option = $this->option->merge($data);
    }



    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }


}