<?php


namespace Commune\Chatbot\OOHost\NLU\Contexts;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;

/**
 * @property-read string $editingName
 */
class IntExampleEditor extends OOContext
{
    const DESCRIPTION = '编辑意图的例句';


    public function __construct(string $editingName)
    {
        parent::__construct(get_defined_vars());
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->talk(function(Dialog $dialog) {
            $repo = $this->getRepo();
            $corpus = $this->getCorpus();
            $name = $this->editingName;

            /**
             * @var IntentCorpusOption $intentCorpus
             */
            $intentCorpus = $corpus->intentCorpusManager()->get($name);
            $examples = $intentCorpus->examples;
            $desc = $repo->getDef($name)->getDesc();

            $dialog->say()
                ->askChoose(
                    "正在编辑意图 $name ($desc). 

输入例句的  '序号: 修改内容' 可以修改已有的例句. (例如输入 '0: 把例句改成这样'). 
序号为 'a', 则新增例句. 例句内容为空则删除之.

例句标注entity可以用markdown link语法, 即  [原词语](entityName), 例如:  '请问[明天](time)[北京](location)的天气怎么样'

请操作:",
                    [
                        'b' => '返回',
                    ] + $examples
                );

            return $dialog->wait();

        }, function(Dialog $dialog, Message $message){

            return $dialog->hear($message)
                ->is('b', function(Dialog $dialog) {
                    return $dialog->fulfill();
                })
                ->isInstanceOf(
                    VerbalMsg::class,
                    function(Dialog $dialog, VerbalMsg $msg){

                        $text = $msg->getTrimmedText();

                        if (false === strpos($text, ':')) {
                            return null;
                        }

                        list($index, $modify) = explode(':', $text, 2);
                        $index = trim($index);

                        // 添加例句
                        if ($index === 'a') {
                            return $this->appendExample($dialog, $modify);
                        }


                        if (!is_numeric($index)) {
                            return null;
                        }

                        $index = intval($index);

                        return $this->modifyExample($dialog, $index, $modify);
                    }
                )
                ->end();
        });
    }


    protected function modifyExample(Dialog $dialog, int $index, string $modify) : Navigator
    {
        $name = $this->editingName;
        $corpus = $this->getCorpus();
        /**
         * @var IntentCorpusOption $intentCorpus
         */
        $intentCorpus = $corpus->intentCorpusManager()->get($name);

        $examples = $intentCorpus->examples;

        if (!isset($examples[$index])) {
            $dialog
                ->say()
                ->error("序号 $index 的例句不存在!");
            return $dialog->repeat();
        }

        $modify = trim($modify);
        if (empty($modify)) {
            unset($examples[$index]);
            $line = '已删除';
        } else {
            $examples[$index] = $modify;
            $line = '修改完毕';
        }

        $intentCorpus->resetExamples($examples);
        $corpus->intentCorpusManager()->save($intentCorpus);

        $dialog->say()->info($line);
        return $dialog->restart();
    }


    protected function appendExample(Dialog $dialog, string $text) : Navigator
    {
        $text = trim($text);
        if (empty($text)) {
            $dialog->say()->warning("修改内容不能为空! ");
            return $dialog->repeat();
        }

        /**
         * @var IntentCorpusOption $intentCorpus
         */
        $corpus = $this->getCorpus();
        $intentCorpus = $corpus->intentCorpusManager()->get($this->editingName);

        $intentCorpus->addExample($text);
        $corpus->intentCorpusManager()->save($intentCorpus);

        $dialog->say()->info('添加完毕');
        return $dialog->restart();
    }



    protected function getRepo() : RootIntentRegistrar
    {
        return $this->getSession()->intentRepo;
    }

    protected function getCorpus() : Corpus
    {
        return $this->getSession()->conversation->make(Corpus::class);
    }
}