<?php


namespace Commune\Chatbot\App\Components\SimpleChat\Tasks;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\App\Callables\Actions\ToNext;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Components\NLUExamples\EditIntentTask;
use Commune\Chatbot\App\Components\NLUExamples\NLUExamplesManager;
use Commune\Chatbot\App\Components\SimpleChat\Manager;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntentDef;
use Commune\Chatbot\OOHost\Context\Intent\Registrar;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Exceptions\NavigatorException;
use Commune\Chatbot\OOHost\NLU\NLUExample;

/**
 * @property string $editIndex
 * @property string $editIntent
 * @property bool $isSupervisor
 *
 * @property string $unmatchedText
 *
 *
 */
class ManagerTask extends TaskDef
{
    const DESCRIPTION = '管理 simple chat (可配置的简单聊天)';

    const CONTEXT_TAGS = [Definition::TAG_MANAGER];

    public static function __depend(Depending $depending): void
    {
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
        return $stage->buildTalk()
            ->info("请输入一句话, 我们查看是否命中意图, 默认回复如何")
            ->wait()
            ->hearing()
            ->isInstanceOf(VerboseMsg::class, function(Dialog $dialog, VerboseMsg $msg){

                $incoming = $dialog->session->incomingMessage;
                $intent = $incoming->getMostPossibleIntent();

                if (empty($intent)) {
                    $dialog->say()->warning('没有匹配到任何意图!');
                    $this->unmatchedText = $msg->getTrimmedText();
                    return $dialog->goStage('createPlaceholder');
                }
                $this->editIntent = $intent;


                $possible = $incoming->getPossibleIntentCollection();
                $hits = '';
                foreach ($possible as $name => $odd) {
                    $hits .= "$name : $odd \n";
                }
                $dialog->say()->info("意图可能性如下: \n $hits");


                $reply = Manager::match($this->editIndex, $intent);
                if (empty($reply)) {
                    $dialog->say()->warning("无回复");
                    return $dialog->goStage('initChat');
                }

                $dialog->say()
                    ->info("命中意图 $intent, 预期回复如下:")
                    ->warning($reply);

                return $dialog->goStage('edit');
            })
            ->end();
    }

    /**
     * 是否要为不存在的意图创建一个 placeholder
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onCreatePlaceholder(Stage $stage) : Navigator
    {
        $text = $this->unmatchedText;

        return $stage->buildTalk()
            ->info(
                "是否为例句创建一个意图占位符 (placeholder intent)? \n
例句如下: 
--- 
$text

---

输入意图名称(必须是 字母,数字或.) 则创建意图, 输入为空则表示放弃.
"
            )
            ->wait()
            ->hearing()
            ->isEmpty($next = new ToNext('talkIntent'))
            ->isInstanceOf(
                VerboseMsg::class,
                function(Dialog $dialog, VerboseMsg $msg, NLUExamplesManager $manager){
                    $intent = $msg->getTrimmedText();

                    $matched = ContextRegistrar::validateName($intent);

                    if (!$matched) {
                        $dialog->say()->error('意图命名不合法! 只能是字母+数字加 .');
                        return $dialog->rewind();
                    }

                    // 权限检查
                    if (!$this->isSupervisor) {
                        return $dialog->reject();
                    }

                    $repo = IntentRegistrar::getIns();

                    if ($repo->has($intent)) {
                        $repo->registerNLUExample($intent, new NLUExample($this->unmatchedText));
                        $manager->generate();
                        $dialog->say()->info("例句添加到已定义意图 $intent");

                    } else {
                        $repo->register(new PlaceHolderIntentDef($intent));
                        $repo->registerNLUExample($intent, new NLUExample($this->unmatchedText));
                        $manager->generate();
                        $dialog->say()->info("创建意图完毕");
                    }

                    $this->editIntent = $intent;
                    return $dialog->goStage('doEditIntent');
                }
            )
            ->end($next);

    }

    public function __onEditIntent(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose("请输入意图的名称, 输入'b' 返回")
            ->wait()
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
                $this->editIntent = $name;

                if (!in_array($name, $intents)) {
                    $dialog->say()
                        ->info("intent $name 未在 $editing 配置内注册过");

                    return $dialog->goStage('initChat');
                }

                return $dialog->goStage('doEditIntent');

            })
            ->end();
    }

    /**
     * 初始化一个chat
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onInitChat(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->withSlots([
                'editing' => $this->editIndex,
                'intent' => $this->editIntent,
            ])
            ->askConfirm("是否在 %editing% 内初始化意图 %intent% ? ")
            ->wait()
            ->hearing()
            ->isPositive(function(Dialog $dialog) {
                $this->save($dialog, $this->editIndex, $this->editIntent, []);
                $dialog->say()->info('初始化完毕');
                return $dialog->goStage('doEditIntent');
            })
            ->end(new ToNext('edit'));
    }

    protected function save(
        Dialog $dialog,
        string $index,
        string $intentName,
        array $replies
    ) : void
    {
        if (!$this->isSupervisor) {
            throw new NavigatorException($dialog->reject());
        }
        Manager::setIntentReplies($index, $intentName, $replies);
        Manager::saveResource($index);
        $dialog->say()->info("修改并保存.");

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

        return $stage->buildTalk()
            ->askVerbose(
                '正在编辑分组 ' . $this->editIndex . ' 下的意图 '.$this->editIntent .'
                
输入普通字符串将添加新的回复
输入"序号:修改内容"可已修改, 内容为空则删除.
输入"m" 则继续编辑意图例句
输入"b" 则返回

预定义的回复如下:',
                $replies
            )
            ->wait()
            ->hearing()
            ->is('b', function(Dialog $dialog) {
                return $dialog->goStage('edit');

            })
            ->is('m', new ToNext('editNLU'))
            ->isInstanceOf(VerboseMsg::class, function(Dialog $dialog, VerboseMsg $msg) use ($replies) {

                $text = $msg->getTrimmedText();
                $secs = explode(":", $text, 2);

                if (count($secs) === 1 || !is_numeric($secs[0])) {
                    $replies[] = $text;
                    $this->save($dialog, $this->editIndex, $this->editIntent, $replies);


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
                    $this->save($dialog, $this->editIndex, $this->editIntent, $replies);

                    return $dialog->repeat();
                }


                $replies[$index] = $mod;

                $this->save($dialog, $this->editIndex, $this->editIntent, $replies);
                return $dialog->repeat();

            })
            ->end();

    }

    /**
     * 编辑 NLU
     * @param Stage $stage
     * @return Navigator
     */
    public function __onEditNLU(Stage $stage) : Navigator
    {
        return $stage->dependOn(
            new EditIntentTask($this->editIntent),
            new ToNext('editIntent')
        );
    }


    public function __exiting(Exiting $listener): void
    {
        $listener->onFulfill(function(Dialog $dialog) {
            $dialog->say()
                ->info('结束 simple chat 编辑');
        })->onReject(function(Dialog $dialog) {
            $dialog->say()
                ->error("没有操作权限!");
        });
    }


    public function __getIsSupervisor() : bool
    {
        return $this->getSession()
            ->conversation
            ->isAbleTo(Supervise::class);
    }

}