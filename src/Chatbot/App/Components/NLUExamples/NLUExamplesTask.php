<?php


namespace Commune\Chatbot\App\Components\NLUExamples;


use Commune\Chatbot\App\Callables\Actions\ToNext;
use Commune\Chatbot\App\Callables\Intercepers\MustBeSupervisor;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Callables\StageComponents\Paginator;
use Commune\Chatbot\App\Messages\QA\VbAnswer;
use Commune\Chatbot\App\Traits\AskContinueTrait;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntentDef;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\Corpus\Example as NLUExample;
use Illuminate\Support\Collection;

/**
 * @property string $domain
 * @property string $editingName
 * @property int $page
 *
 */
class NLUExamplesTask extends OOContext
{
    use AskContinueTrait;

    const DESCRIPTION = '管理意图的 NLU 样本';

    const CONTEXT_TAGS = [
        Definition::TAG_MANAGER
    ];

    protected $limit = 15;

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
        $stage->onStart(new MustBeSupervisor());
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        $repo = $this->getSession()->intentRepo;
        $intentCount = $repo->countIntentsHasNLUExamples();
        $expCount = $repo->countNLUExamples();
        return $stage->buildTalk()
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
                '强制保存' => function(Dialog $dialog, NLUExamplesManager $manager) {
                    $manager->generate();
                    $dialog->say()->info('保存成功');
                    return $dialog->repeat();
                }
            ]
        ));
    }


    public function __onList(Stage $stage) : Navigator
    {
        $repo = $this->getRepo();
        $all = $repo->getNLUExampleMapByIntentDomain($this->domain);
        $totalPage = (int) ceil(count($all) / $this->limit);

        return $this->doPaginate(
            $stage,
            $totalPage,
            function(Context $self, Dialog $dialog, int $offset, int $limit) use ($all){
                return (new Collection($all))->splice($offset, $limit);
            },
            function(Context $self, Dialog $dialog, Collection $items){

                $list = [];
                $repo = $this->getRepo();

                foreach ($items as $name => $collection) {
                    $count = count($collection);
                    $desc = $repo->getDef($name)->getDesc();

                    $list[] = "$name ($count) : $desc";
                }

                $dialog->say()->info(implode("\n", $list));

            }
        );
    }


    /**
     * 列出domain下的所有意图.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onListIntents(Stage $stage) : Navigator
    {
        $repo = $this->getRepo();
        $total = count($repo->getDefNamesByDomain($this->domain));
        $totalPage = (int) ceil($total / $this->limit);

        return $this->doPaginate(
            $stage,
            $totalPage,
            function(Context $self, Dialog $dialog, int $offset, int $limit){
                $repo = $this->getRepo();
                $all = $repo->getDefNamesByDomain($this->domain);
                return (new Collection($all))->splice($offset, $limit);
            },
            function(Context $self, Dialog $dialog, Collection $items){

                $list = [];
                $repo = $this->getRepo();

                foreach ($items as $name) {
                    $count = $repo->countNLUExamples($name);
                    $desc = $repo->getDef($name)->getDesc();
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

        $domain = $this->domain;
        $paginator = $paginator
            ->withIntro("
输入字符串将认为是domain, 只展示domain下的数据.
当前domain:$domain \n")
            ->withLimit($this->limit);

        return $stage->component($paginator);
    }

    protected function getRepo() : IntentRegistrar
    {
        return $this->getSession()->intentRepo;
    }

    /**
     * 尝试编辑一个意图的nlu
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onShow(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose('请输入意图的名称:')
            ->wait()
            ->hearing()
            ->isAnswer(function(Dialog $dialog, Answer $answer){
                $name = (string) $answer->toResult();

                $this->editingName = $name;
                $repo = $this->getRepo();
                if ($repo->hasDef($name) || $repo->countNLUExamples($name) > 0) {
                    return $dialog->goStage('editIntent');
                }

                $dialog->say()->warning("intent $name 不存在!");
                return $dialog->goStage('createPlaceholder');
            })
            ->end();
    }

    /**
     * 尝试创建一个占位符.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onCreatePlaceholder(Stage $stage) : Navigator
    {

        return $stage->buildTalk()
            ->withSlots(['editing' => $this->editingName])
            ->askConfirm('是否创建 %editing% 的占位符?')
            ->wait()
            ->hearing()
            ->isPositive(function(Dialog $dialog){
                $this->getRepo()->registerDef(new PlaceHolderIntentDef($this->editingName), false);
                return $dialog->goStage('editIntent');

            })
            ->end(new ToNext('manager'));
    }

    /**
     * 编辑一个意图的 nlu
     * @param Stage $stage
     * @return Navigator
     */
    public function __onEditIntent(Stage $stage) : Navigator
    {
        return $stage->dependOn(
            new EditIntentTask($this->editingName),
            new ToNext('manager')
        );
    }



    /**
     * @deprecated
     * @param Stage $stage
     * @return Navigator
     */
    public function __onAddExample(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose('请输入要添加的例句:')
            ->wait()
            ->hearing()
            ->isAnswer(function (Dialog $dialog, VbAnswer $answer, NLUExamplesManager $manager) {
                $text = $answer->toResult();
                $manager->register($this->editingName, new NLUExample($text));

                $dialog->say()->info('添加完毕');
                return $dialog->goStage('editIntent');
            })
            ->end();

    }



    public function __exiting(Exiting $listener): void
    {
        $listener->onFulfill(function(Dialog $dialog){
            $dialog->say()
                ->info('结束意图例句管理');
        });
    }
}