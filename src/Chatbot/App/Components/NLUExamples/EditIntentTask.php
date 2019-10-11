<?php


namespace Commune\Chatbot\App\Components\NLUExamples;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\Corpus\IntExample as NLUExample;

/**
 * @property-read string $editingName
 */
class EditIntentTask extends OOContext
{
    const DESCRIPTION = '编辑意图的例句';


    public function __construct(string $editingName)
    {
        parent::__construct(get_defined_vars());
    }

    public static function __depend(Depending $depending): void
    {
    }

    protected function getRepo() : IntentRegistrar
    {
        return $this->getSession()->intentRepo;
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->talk(function(Dialog $dialog) {
            $repo = $this->getRepo();

            $name = $this->editingName;
            $examples = $repo->getNLUExamplesByIntentName($name);
            $desc = $repo->getDef($name)->getDesc();

            $exp = array_map(function(NLUExample $example){
                return  $example->originText;
            }, $examples);

            $dialog->say()
                ->askChoose(
                    "正在编辑意图 $name ($desc). 

输入例句的  '序号: 修改内容' 可以修改已有的例句. (例如输入 '0: 把例句改成这样'). 
序号为 'a', 则新增例句. 例句内容为空则删除之.

例句标注entity可以用markdown link语法, 即  [原词语](entityName), 例如:  '请问[明天](time)[北京](location)的天气怎么样'

请操作:",
                    [
                        'b' => '返回',
                    ] + $exp
                );

            return $dialog->wait();

        }, function(Dialog $dialog, Message $message){

            return $dialog->hear($message)
                ->is('b', function(Dialog $dialog) {
                    return $dialog->fulfill();
                })
                ->isInstanceOf(
                    VerboseMsg::class,
                    function(Dialog $dialog, VerboseMsg $msg){

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
        $repo = $this->getRepo();

        $examples = $repo->getNLUExamplesByIntentName($name);
        if (!isset($examples[$index])) {
            $dialog->say()
                ->error("序号 $index 的例句不存在!");
            return $dialog->repeat();
        }

        $modify = trim($modify);
        if (empty($modify)) {
            unset($examples[$index]);
            $line = '已删除';
        } else {
            $examples[$index] = new NLUExample(trim($modify));
            $line = '修改完毕';
        }

        $repo->setIntentNLUExamples($name, $examples);

        $manager = $dialog->app->make(NLUExamplesManager::class);
        $manager->generate();

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

        $repo = $this->getRepo();
        $repo->registerNLUExample($this->editingName, new NLUExample($text));
        $manager = $dialog->app->make(NLUExamplesManager::class);
        $manager->generate();

        $dialog->say()->info('添加完毕');
        return $dialog->restart();
    }


    public function __exiting(Exiting $listener): void
    {
    }


}