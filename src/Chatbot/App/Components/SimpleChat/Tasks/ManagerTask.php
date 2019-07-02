<?php


namespace Commune\Chatbot\App\Components\SimpleChat\Tasks;


use Commune\Chatbot\App\Callables\Intercepers\MustBeSupervisor;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Components\SimpleChat\Manager;
use Commune\Chatbot\App\Components\SimpleChat\SimpleChatAction;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @property-read string $editIndex
 * @property-read string $editIntent
 */
class ManagerTask extends TaskDef
{
    const DESCRIPTION = '管理 simple chat (可配置的简单聊天)';

    const CONTEXT_TAGS = [Definition::TAG_MANAGER];

    public static function __depend(Depending $depending): void
    {
    }

    public function __staging(Stage $stage) : void
    {
        $stage->onStart(new MustBeSupervisor());
    }

    public function __hearing(Hearing $hearing) : void
    {
        $hearing
            ->is('.exit', function(Dialog $dialog){
                return  $dialog->fulfill();
            })->is('.restart', function(Dialog $dialog) {
                return $dialog->restart();
            });
    }

    protected static function getContextName(): string
    {
        return 'simpleChat.manager';
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->talk(function(Dialog $dialog){

            $talk = $dialog->say();
            $resources = Manager::listResources();

            $desc = [];
            foreach ($resources as $index => $resource) {
                $desc[] = "$index : $resource";
            }

            $talk->info(
                "进入简单配置管理, 输入 .exit 随时退出, .restart 重新开始.
已加载的配置如下 ( 分组名: 文件路径 ):\n\n"
                . implode("\n", $desc)
            );

            $talk->askVerbose('请输入一个分组名, 进行管理');
            return $dialog->wait();

        }, function(Dialog $dialog, Message $message){

            return $dialog->hear($message)
                ->isInstanceOf(VerboseMsg::class, function(Dialog $dialog, VerboseMsg $message){

                    $index = (string) $message->getTrimmedText();

                    if (! Manager::hasPreload($index)) {
                        $dialog->say()
                            ->error("分组 $index 不存在");

                        return $dialog->wait();
                    }

                    $this->editIndex = $index;
                    return $dialog->goStage('edit');
                })
                ->end();
        });
    }

    public function __onEdit(Stage $stage) : Navigator
    {
        $index = $this->editIndex;
        return $stage->component(new Menu(
            "当前管理的配置为 $index, 请选择操作:",
            [
                '查看可匹配的意图' => function(Dialog $dialog) {
                    $index = $this->editIndex;

                    $intents = Manager::listResourceIntents($index);

                    $results = [];
                    $repo = IntentRegistrar::getIns();
                    foreach ($intents as $intent) {
                        if ($repo->has($intent)) {
                            $results[] = "- $intent : "
                                . $repo->get($intent)->getDesc();
                        }
                    }

                    $dialog->say()
                        ->info("已注册的意图有:\n".implode("\n", $results));

                    return $dialog->repeat();
                },
                '模拟正常对话来管理' => 'talkIntent',
                '选择意图进行管理 ( 不存在则创建 )' => 'editIntent',
                '返回' => 'start',
            ]
        ));
    }


    public function __onTalkIntent(Stage $stage) : Navigator
    {
        return $stage->build()
            ->info("请输入一句话, 我们查看是否命中意图, 默认回复如何")
            ->callback()
            ->hearing()
            ->isInstanceOf(VerboseMsg::class, function(Dialog $dialog, VerboseMsg $msg){

                $intent = $dialog->session->incomingMessage->getMostPossibleIntent();

                if (empty($intent)) {
                    $dialog->say()->warning('没有匹配到任何意图!');
                    return $dialog->repeat();
                }

                $dialog->say()->info("命中意图 $intent, 预期回复如下:");

                $reply = Manager::match($this->editIndex, $intent);

                if (empty($reply)) {
                    $dialog->say()->warning("无回复");
                } else {
                    SimpleChatAction::reply($this, $dialog, $reply);
                }

                return $dialog->goStage('edit');
            })
            ->end();
    }

    public function __onEditIntent(Stage $stage) : Navigator
    {
        return $stage->build()
            ->askVerbose("请输入意图的名称, 输入'b' 返回")
            ->callback()
            ->hearing()
            ->is('b', function(Dialog $dialog){

                return $dialog->goStage('edit');
            })
            ->isAnswer(function(Dialog $dialog, Answer $answer) {
                $name = (string) $answer->toResult();

                $repo = $dialog->session->intentRepo;

                if (!$repo->has($name)) {
                    $dialog->say()->error("intent $name 不存在或未加载!");
                    return $dialog->repeat();
                }

                $intents = Manager::listResourceIntents($this->editIndex);
                $editing = $this->editIndex;

                if (!in_array($name, $intents)) {
                    $dialog->say()
                        ->info("intent $name 未在 $editing 配置内注册过, 将初始化");
                    Manager::setIntentReplies($this->editIndex, $name, []);
                }

                $this->editIntent = $name;
                return $dialog->goStage('doEditIntent');

            })
            ->end();
    }

    public function __onDoEditIntent(Stage $stage) : Navigator
    {
        $replies = Manager::matchReplies($this->editIndex, $this->editIntent);

        $operation = array_map(function(string $item){
            if (mb_strlen($item) > 100) {
                return mb_substr($item, 0, 100) . '...';
            }
            return $item;

        }, $replies);

        $operation['a'] = '添加新回复';

        return $stage->build()
            ->askVerbose(
                '正在编辑分组 ' . $this->editIndex . ' 下的意图 '.$this->editIntent .'
                
输入普通字符串将添加新的回复
输入"序号:修改内容"可已修改, 内容为空则删除.
输入"b" 则返回

预定义的回复如下:',
                $replies
            )
            ->callback()
            ->hearing()
            ->is('b', function(Dialog $dialog) {
                return $dialog->goStage('edit');

            })
            ->isInstanceOf(VerboseMsg::class, function(Dialog $dialog, VerboseMsg $msg) use ($replies) {


                $text = $msg->getTrimmedText();
                $secs = explode(":", $text, 2);

                if (count($secs) === 1 || !is_numeric($secs[0])) {
                    $replies[] = $text;
                    Manager::setIntentReplies($this->editIndex, $this->editIntent, $replies);
                    Manager::saveResource($this->editIndex);

                    $dialog->say()->info("添加完毕");
                    return $dialog->repeat();
                }

                $index = trim($secs[0]);
                $mod = trim($secs[1]);

                if (!isset($replies[$index])) {
                    $dialog->say()->warning("序号 $index 不存在!");
                    return $dialog->repeat();
                }

                if (mb_strlen($mod) === 0) {
                    unset($replies[$index]);
                    Manager::setIntentReplies($this->editIndex, $this->editIntent, $replies);
                    Manager::saveResource($this->editIndex);
                    $dialog->say()->info("删除了第 $index 条");
                    return $dialog->repeat();
                }


                $replies[$index] = $mod;
                Manager::setIntentReplies($this->editIndex, $this->editIntent, $replies);
                Manager::saveResource($this->editIndex);
                $dialog->say()->info("修改了第 $index 条");
                return $dialog->repeat();

            })
            ->end();

    }


    public function __exiting(Exiting $listener): void
    {
        $listener->onFulfill(function(Dialog $dialog) {
            $dialog->say()
                ->info('结束 simple chat 编辑');
        });
    }


}