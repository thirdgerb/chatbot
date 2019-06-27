<?php


namespace Commune\Chatbot\App\Components\IntentManager;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Callables\StageComponents\Paginator;
use Commune\Chatbot\App\Components\IntentManagerComponent;
use Commune\Chatbot\App\Messages\QA\VbAnswer;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\Registrar;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\NLUExample;
use Illuminate\Support\Collection;

/**
 * @property string $domain
 * @property string $editingName
 * @property int $page
 *
 */
class NLUExamplesManager extends OOContext
{
    const DESCRIPTION = '管理意图的 NLU 样本';

    protected $limit = 30;

    public function __construct()
    {
        parent::__construct([
            'domain' => '',
            'page' => 0
        ]);
    }

    public static function getContextName(): string
    {
        return 'nlu.examples.manager';
    }

    public function staging(Stage $stage)
    {
        $stage->onStart(function(Dialog $dialog) : ? Navigator{
            $isSupervisor = $dialog->session
                ->conversation
                ->isAbleTo(Supervise::class);
            if (!$isSupervisor) {
                return $dialog->reject();
            }

            return null;
        });
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        $repo = IntentRegistrar::getIns();
        $intentCount = $repo->countIntentsHasNLUExamples();
        $expCount = $repo->countNLUExamples();
        return $stage->build()
            ->info("对意图(intent) 的例句进行管理.(随时输入.exit退出)
可为意图查看, 添加例句.
共有$intentCount 个intent配置了共$expCount 条例句.")
            ->goStage('manager');
    }

    public function __hearing(Hearing $hearing) : void
    {
        $hearing->is('.exit', function(Dialog $dialog){
             return $dialog->fulfill();
        });
    }

    public function __onManager(Stage $stage) : Navigator
    {
        return $stage->component(new Menu(
            '请选择操作',
            [
                '查看有例句的意图列表' => 'list',
                '查看意图列表' => 'listIntents',
                '编辑单个意图的例句' => 'show',
                '退出' => function(Dialog $dialog) {
                    return $dialog->fulfill();
                },
                '强制保存' => function(Dialog $dialog) {
                    $repo = $this->getRepo();
                    $this->doSave($dialog, $repo);
                    $dialog->say()->info('保存成功');
                    return $dialog->repeat();
                }
            ]
        ));
    }


    public function __onList(Stage $stage) : Navigator
    {
        $repo = $this->getRepo();
        $totalPage = (int) ceil($repo->countIntentsHasNLUExamples() / $this->limit);

        return $this->doPaginate(
            $stage,
            $totalPage,
            function(Context $self, Dialog $dialog, int $offset, int $limit){
                $repo = $this->getRepo();
                $all = $repo->getNLUExampleMapByIntentDomain($this->domain);
                return (new Collection($all))->splice($offset, $limit);
            },
            function(Context $self, Dialog $dialog, Collection $items){

                $list = [];
                $repo = $this->getRepo();

                foreach ($items as $name => $collection) {
                    $count = $collection->count();
                    $desc = $repo->get($name)->getDesc();

                    $list[] = "$name ($count) : $desc";
                }

                $dialog->say()->info(implode("\n", $list));

            }
        );
    }


    public function __onListIntents(Stage $stage) : Navigator
    {
        $repo = $this->getRepo();
        $totalPage = (int) ceil($repo->count() / $this->limit);

        return $this->doPaginate(
            $stage,
            $totalPage,
            function(Context $self, Dialog $dialog, int $offset, int $limit){
                $repo = $this->getRepo();
                $all = $repo->getNamesByDomain($this->domain);
                return (new Collection($all))->splice($offset, $limit);
            },
            function(Context $self, Dialog $dialog, Collection $items){

                $list = [];
                $repo = $this->getRepo();

                foreach ($items as $name) {
                    $count = $repo->countNLUExamples($name);
                    $desc = $repo->get($name)->getDesc();
                    $list[] = "$name ($count) : $desc";
                }

                $dialog->say()->info(implode("\n", $list));

            }
        );

    }

    protected function doPaginate(
        Stage $stage,
        int $total,
        callable $paginate,
        callable $listing
    ) : Navigator
    {
        $paginator = new Paginator(
            $total,
            $paginate,
            $listing,
            [
                'b: 返回' => function(Dialog $dialog){
                    return $dialog->goStage('manager');
                },
                'm: 编辑意图例句' => function (Dialog $dialog) {
                    return $dialog->goStage('show');
                }
            ],
            function(Dialog $dialog, Message $message) {
                if ($message instanceof VerboseMsg) {
                    $domain =  $message->getTrimmedText();
                    $this->domain = $domain;
                    return $dialog->repeat();
                }

                return null;
            }
        );

        $paginator->introduce .= "\n 输入字符串将认为是domain, 只展示domain下的数据";
        $paginator->limit = $this->limit;
        return $stage->component($paginator);
    }

    protected function getRepo() : Registrar
    {
        return IntentRegistrar::getIns();
    }

    public function __onShow(Stage $stage) : Navigator
    {
        return $stage->build()
            ->askVerbose('请输入意图的名称:')
            ->callback()
            ->hearing()
            ->isAnswer(function(Dialog $dialog, Answer $answer){
                $name = (string) $answer->toResult();

                $repo = $this->getRepo();
                if ($repo->has($name)) {
                    $this->editingName = $name;
                    return $dialog->goStage('editIntent');
                }

                $dialog->say()->error("intent $name 不存在!");
                return $dialog->goStage('manager');
            })
            ->end();
    }

    public function __onEditIntent(Stage $stage) : Navigator
    {
        return $stage->talk(function(Dialog $dialog) {
            $repo = $this->getRepo();

            $name = $this->editingName;
            $examples = $repo->getNLUExamplesByIntentName($name);
            $desc = $repo->get($name)->getDesc();

            $exp = array_map(function(NLUExample $example){
                return  $example->originText;
            }, $examples);

            $dialog->say()
                ->askChoose(
                    "
正在编辑意图 $name ($desc). 


输入例句的  '序号: 修改内容' 可以修改已有的例句. (例如输入 '0: 把例句改成这样')

例句标注entity可以用markdown link语法, 即  [原词语](entityName), 例如:  '请问[明天](time)[北京](location)的天气怎么样'

请操作:",
                    [
                        'a' => '增加例句',
                        'b' => '返回',
                    ] + $exp
                );

            return $dialog->wait();

        }, function(Dialog $dialog, Message $message){

            return $dialog->hear($message)
                ->is('a', function(Dialog $dialog){
                    return $dialog->goStage('addExample');
                })
                ->is('b', function(Dialog $dialog) {
                    return $dialog->goStage('manager');
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

                        if (!is_numeric($index)) {
                            return null;
                        }

                        $index = intval($index);
                        $repo = $this->getRepo();
                        $name = $this->editingName;
                        $examples = $repo->getNLUExamplesByIntentName($name);

                        if (!isset($examples[$index])) {
                            $dialog->say()
                                ->error("序号 $index 的例句不存在!");
                            return $dialog->repeat();
                        }

                        $examples[$index] = new NLUExample(trim($modify));
                        $repo->setIntentNLUExamples($name, $examples);
                        $this->doSave($dialog, $repo);

                        $dialog->say()->info('修改完毕');
                        return $dialog->repeat();
                    }
                )
                ->end();
        });
    }


    public function __onAddExample(Stage $stage) : Navigator
    {
        return $stage->build()
            ->askVerbose('请输入要添加的例句:')
            ->callback()
            ->hearing()
            ->isAnswer(function (Dialog $dialog, VbAnswer $answer) {
                $text = $answer->toResult();
                $repo = $this->getRepo();

                $repo->registerNLUExample($this->editingName, new NLUExample($text));
                $this->doSave($dialog, $repo);
                $dialog->say()->info('添加完毕');
                return $dialog->goStage('editIntent');
            })
            ->end();

    }


    protected function doSave(Dialog $dialog, Registrar $registrar) : void
    {
        /**
         * @var IntentManagerComponent $option
         */
        $option = $dialog->app->make(IntentManagerComponent::class);
        $all = $registrar->getNLUExamplesCollection();

        $data = [];
        foreach ($all as $intentName => $examples) {
            foreach ($examples as $example) {
                /**
                 * @var NLUExample $example
                 */
                $data[$intentName][] = $example->originText;
            }
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($option->repository, $json);
    }



    public function __exiting(Exiting $listener): void
    {
        $listener->onFulfill(function(Dialog $dialog){
            $dialog->say()
                ->info('结束意图例句管理');
        });
    }
}