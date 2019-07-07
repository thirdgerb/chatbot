<?php


namespace Commune\Chatbot\App\Components\OptionEdit;


use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Support\Option;

/**
 * @property Option $option
 * @property string $modifyKey
 * @property bool $changed
 * @property-read string[] $optionPropSuggestions
 */
class OptionEditor extends OOContext
{
    const DESCRIPTION = '';

    protected $welcome  = '编辑option';

    private static $editors = [];

    private function __construct(Option $option)
    {
        parent::__construct([
            'saved' => false,
            'changed' => false,
            'option' => $option
        ]);
    }

    final public static function register(string $optionName, string $editorClass) {
        self::$editors[$optionName] = $editorClass;
    }

    /**
     * @param Option $option
     * @return OptionEditor
     */
    final public static function make(Option $option)
    {
        $clazz = self::$editors[get_class($option)] ?? self::class;
        return new $clazz($option);
    }


    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info($this->welcome)
            ->goStage('show');
    }

    public function __onShow(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->interceptor(function(Dialog $dialog) {
                $dialog->say()
                    ->info(
                        '正在编辑对象: '.get_class($this->option)
                        ."\nid : ".$this->option->getId()
                        ."\ndesc : " . $this->option->getDescription())
                    ->info(implode("\n", $this->optionPropSuggestions));

            })->goStage('menu');

    }

    public function __onMenu(Stage $stage) : Navigator
    {
        return $stage->component(new Menu(
                '您的操作是:',
                [
                    '编辑键值' => 'modify',
                    '预览数据' => function(Dialog $dialog){
                        $dialog->say()
                            ->info("数据如下: ")
                            ->info($this->option->toPrettyJson());
                        return $dialog->repeat();
                    },
                    '查看对象结构' => 'show',
//                    '保存' => '',
                    '完成' => 'final',
                ]
            ));
    }

    public function __onModify(Stage $stage) : Navigator
    {
        $options = $this->optionPropSuggestions;
        $options['b'] = '返回上一层';

        return $stage->buildTalk()
            ->askChoose(
                '选择需要编辑的键',
                $options
            )
            ->wait()
            ->hearing()
                ->isChoice('b', function(Dialog $dialog){
                    return $dialog->fulfill();
                })
                ->isAnswer(function(Dialog $dialog, Answer $answer){

                    $choice = $answer->getChoice();
                    $props = array_values($this->option->getProperties());
                    $prop = $props[$choice] ?? [];

                    // 理论上不会发生.
                    if (empty($prop)) {
                        $dialog->say()
                            ->error("错误的选项: $choice");
                        return $dialog->repeat();
                    }

                    list($name, $type, $desc) = $prop;
                    $this->modifyKey = $name;

                    // 默认可以通过 stage 重写.
                    if ($this->getDef()->hasStage($name)) {
                        return $dialog->goStage($name);
                    }

                    if ($this->option->isAssociation($name)) {
                        return $dialog->goStage('editAssociation');
                    }

                    if ($this->option->isListAssociation($name)) {
                        return $dialog->goStage('editListAssociation');
                    }

                    $value = $this->option->{$name};
                    if (is_array($value)) {
                        return $dialog->goStage('editList');
                    }

                    return $dialog->goStage('editValue');
                })
                ->end();
    }

    public function __onEditValue(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->dependOn(new KeyEditor($this->option, $this->modifyKey))
            ->action(function(Dialog $dialog, KeyEditor $callback) {
                return $this->saveChange($callback->option, $dialog);
            });
    }

    protected function saveChange(Option $option, Dialog $dialog) : Navigator
    {
        $this->option = $option;
        $dialog->say()->info("保存改动");
        $this->changed = true;
        return $dialog->goStage('modify');
    }

    public function __onFinal(Stage $stage) : Navigator
    {
        return $stage->dialog->fulfill();
    }

//    public function __onEditAssociation(Stage $stage) : Navigator
//    {
//
//
//    }

    public function __exiting(Exiting $listener): void
    {
        $listener->onCancel(function(Dialog $dialog){
            $dialog->say()->info('回到option修改');
            return $dialog->goStage('show');
        });
    }

    public static function __depend(Depending $depending): void
    {
    }

    protected function __getOptionPropSuggestions()
    {
        $suggestions = [];
        foreach ($this->option->getProperties() as list($name, $type, $desc)) {
            $suggestions[] = "$name (类型: $type ) : $desc";
        }
        return $suggestions;
    }
}