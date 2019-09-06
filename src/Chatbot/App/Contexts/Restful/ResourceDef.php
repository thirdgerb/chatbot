<?php


namespace Commune\Chatbot\App\Contexts\Restful;


use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\App\Callables\StageComponents\Menu;

/**
 * restful 风格的多轮对话控制器. 用来做一些管理类的操作.
 *
 * @property array $paths [string key => string value ]
 * @property string|null|int $id  当前资源的id. 为null 表示是列表页
 *
 *
 * @property int $errCode 异常码. 为0 表示正常.
 *
 * @property int $page  当前所在页数. 默认为0.
 *
 *
 * 以下是 getter 方法实现的变量
 *
 * @property-read string $uri  uri的结构是  domain:source/id/source2/id
 * @property-read bool $isList 当前节点是列表节点, 例如  books/
 * @property-read bool $isResource 当前节点是资源节点, 例如 books/17  给出了最后一个元素的ID
 * @property-read int $pageIndex page的实际序号. 0开始.
 * @property-read int $offset 列表页的实际偏移量.
 *
 *
 *
 * 以下是跳转时用到的临时变量.
 *
 * @property-read string|int|null $redirectId
 * @property ResourceDef|null $redirect  导航到别的
 *
 *
 */
abstract class ResourceDef extends OOContext implements ResourceHelper
{

    /*------- constant -------*/

    const ERR_URI_INVALID = 400;
    const ERR_NOT_FOUND = 404;

    const ERR_MESSAGES = [
        self::ERR_URI_INVALID => 'uri 参数不正确',
        self::ERR_NOT_FOUND => '资源不存在',
    ];

    /*------- 必须定义的配置. -------*/

    // 资源的所在域. 必填. 没有 '.' 的字符串.
    const MODULE = '';

    // 资源对应的key, 有先后顺序, 最后一个是自己.
    const PATHS = [];

    // 资源自己 ID 的名称.
    const IDENTITY = '';

    const DESCRIPTION = '';


    /*------- 约定 -------*/

    /**
     * 每页展示数量.
     * @var int
     */
    protected $limit = 20;

    protected $askOperation = '请选择操作:';

    protected $askForPage = '请输入页数';

    protected $pageOfTotal = '当前第 %page% 页 / 共 %total% 页';

    protected $askForId = '请输入id : ';

    protected $success = '操作成功';

    protected $failure = '操作失败: %error%';

    protected $askCreateResource = '是否创建元素 id = %id%?';

    protected $askConfirmDelete = '是否删除元素 id = %id%?';

    protected $resourceNotExists = '资源 %id% 不存在';

    protected $showResourceDetail = "展示数据详情:\n%detail%";

    protected $askConfirmSave = '确认保存?';

    protected $pageIsEmpty = '本页没有数据';

    /**
     * 默认的操作
     * @var array
     */
    protected $listMenu = [
        '查看/修改/创建' => 'editResource',
        '选择页数' => 'page',
        '删除' => 'deleteResource',
    ];

    /**
     * 默认的资源操作.
     * @var array
     */
    protected $resourceMenu = [
        '查看详细数据' => 'resourceDetail',
        '前往列表' => 'listResource',
        '自我删除' => 'selfDelete',
    ];

    /*------- 只用一次的变量, 无需存储 -------*/

    final public function __construct(array $paths = [], $id = null)
    {
        $errCode = 0;
        $page = 1;
        parent::__construct(get_defined_vars());
    }

    /**
     * 检查资源是否存在
     *
     * @param Dialog $dialog
     * @param array $paths
     * @param string|int $id
     * @return bool
     */
    abstract function isResourceExists(Dialog $dialog, array $paths, $id) : bool;

    /**
     * 检查列表的位置是否正确.
     *
     * @param Dialog $dialog
     * @param array $paths
     * @return bool
     */
    abstract function isListExists(Dialog $dialog, array $paths) : bool;

    /**
     * 列表信息展示.
     *
     * @param Dialog $dialog
     * @param array $paths
     * @param int $offset
     * @param int $limit
     * @return array [ id => desc ]
     */
    abstract function paginate(Dialog $dialog, array $paths, int $offset, int $limit) : array ;

    /**
     * @param Dialog $dialog
     * @param array $paths
     * @return int
     */
    abstract function listTotal(Dialog $dialog, array $paths) : int;


    /**
     * 创建一个资源.
     * @param Dialog $dialog
     * @param string[] $paths
     * @param int|string $id
     * @return null|string 为null表示成功.
     */
    abstract function createResource(Dialog $dialog, array $paths, $id) : ? string;

    /**
     * @param Dialog $dialog
     * @param array $paths
     * @param string $id
     * @return null|string
     */
    abstract function deleteResource(Dialog $dialog, array $paths, $id) : ? string;


    /**
     * 资源信息展示.
     *
     * @param Dialog $dialog
     * @param array $paths
     * @param int|string $id
     * @return string
     */
    abstract function showResourceDetail(Dialog $dialog, array $paths, $id) : string;

    /**
     * 保存当前改动.
     * @param Dialog $dialog
     * @param array $paths
     * @param string|int|null $id
     * @return string
     */
    abstract function saveResource(Dialog $dialog, array $paths, $id) : ? string;


    /**
     * @param Dialog $dialog
     * @return Navigator|null
     */
    abstract function resourceView(Dialog $dialog) : ? Navigator;


    abstract function itemsView(Dialog $dialog, array $items) : ? Navigator;

    function listView(Dialog $dialog) : ? Navigator
    {

        $total = $this->listTotal($dialog, $this->paths);
        $page = $this->page;

        $totalPage = ceil($total/$this->limit);

        $dialog->say()->info($this->pageOfTotal, [
            '%page%' => $page,
            'total' => $totalPage, // 故意测试一下不加 % 是否正确补完.
        ]);

        $items = $this->paginate($dialog, $this->paths, $this->offset, $this->limit);

        if (empty($items)) {
            $dialog->say()->warning($this->pageIsEmpty);
            return null;
        }

        $data = [];
        foreach ($items as $index => $value) {
            $data[] = " - [$index] : $value";
        }
        $info = "本页数据如下: \n" . implode("\n", $data);
        $dialog->say()->info($info);
        return null;
    }



    public function __onStart(Stage $stage): Navigator
    {
        if ($this->errCode === self::ERR_URI_INVALID) {
            return $stage->dialog->fulfill();
        }

        if (
            $this->isResource
            && $this->isResourceExists($stage->dialog, $this->paths, $this->id)
        ) {
            return $stage->dialog->goStage('resource');
        }

        if ($this->isList && $this->isListExists($stage->dialog, $this->paths)) {
            return $stage->dialog->goStage('list');
        }

        $this->errCode = self::ERR_NOT_FOUND;
        return $stage->dialog->fulfill();
    }

    public function __onResource(Stage $stage) : Navigator
    {
        return $stage
            ->onStart([$this, 'showLocation'])
            ->onStart([$this, 'resourceView'])
            ->component(new Menu(
                $this->askOperation,
                $this->getResourceMenu()
            ));
    }

    public function showLocation(Dialog $dialog) : void
    {
        $dialog->say()->info(
            '当前context : '. $this->getName()
            . "\n当前位置: " . $this->uri
        );
    }

    public function __onResourceDetail(Stage $stage) : Navigator
    {
        $detail = $this->showResourceDetail(
            $stage->dialog,
            $this->paths,
            $this->id
        );
        return $stage->buildTalk()
            ->info(
                $this->showResourceDetail,
                [
                    'detail' => $detail
                ]
            )->goStage('start');
    }

    /**
     * 列表页首页
     * @param Stage $stage
     * @return Navigator
     */
    public function __onList(Stage $stage): Navigator
    {
        return $stage
            ->onStart([$this, 'showLocation'])
            ->onStart([$this, 'listView'])
            ->component(new Menu(
                $this->askOperation,
                $this->getListMenu(),
                null,
                null,
                function(Hearing $hearing) {

                    $hearing->isInstanceOf(VerboseMsg::class, function(Dialog $dialog, VerboseMsg $msg){
                        $text= $msg->getTrimmedText();
                        $items = $this->paginate($dialog, $this->paths, $this->offset, $this->limit);
                        if (array_key_exists($text, $items)) {
                            return $this->toSelfResource($dialog, $text);
                        }
                        return null;
                    });
                }
            ));
    }

    protected function getListMenu() : array
    {
        return $this->listMenu;
    }

    protected function getResourceMenu() : array
    {
        return $this->resourceMenu;
    }

    /**
     * 选择列表页数.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onPage(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose($this->askForPage)
            ->wait()
            ->hearing()
                ->isAnswer(function(Dialog $dialog, Answer $answer){
                    $page = intval($answer->toResult());

                    $dialog->say()->info("选择了第 $page 页");
                    $this->page = $page;

                    return $dialog->goStage('list');
                })

                ->end();
    }



    public function __onListResource(Stage $stage) : Navigator
    {
        return $this->toSelfResource($stage->dialog, null);
    }


    public function __onEditResource(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose($this->askForId)
            ->wait()
            ->hearing()
            ->isAnswer(function(Dialog $dialog, Answer $answer){
                $id = $answer->toResult();
                if ($this->isResourceExists($dialog, $this->paths, $id)) {
                    return $this->toSelfResource($dialog, $id);
                }

                $this->redirectId = $id;
                return $dialog->goStage('createResource');
            })
            ->end();
    }

    public function __onCreateResource(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->withSlots(['%id%' => $this->redirectId])
            ->askConfirm(
                $this->askCreateResource,
                true
            )
            ->wait()
            ->hearing()
            ->isChoice(1, function(Dialog $dialog){
                $error = $this->createResource($dialog, $this->paths, $this->redirectId);

                if (empty($error)) {
                    $dialog->say()->info($this->success);
                    return $this->toSelfResource($dialog, $this->redirectId);
                }

                $dialog->say(['error' => $error])->error($this->failure);
                return $dialog->restart();

            })
            ->isChoice(0, function(Dialog $dialog){
                return $dialog->restart();

            })
            ->end();
    }

    public function __onDelResource(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose($this->askForId)
            ->wait()
            ->hearing()
            ->isAnswer(function (Dialog $dialog, Answer $answer) {
                $id = $answer->toResult();
                $this->redirectId = $id;
                return $dialog->goStage('confirmDelete');

            })->end();
    }

    public function __onSelfDelete(Stage $stage) : Navigator
    {
        $this->redirectId = $this->id;
        return $stage->dialog->goStage('confirmDelete');
    }

    public function __onConfirmDelete(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->withSlots(['id' => $this->redirectId])
            ->askConfirm($this->askConfirmDelete)
            ->wait()
            ->hearing()
            ->isChoice(0, function(Dialog $dialog){
                return $dialog->restart();
            })
            ->isChoice(1, function(Dialog $dialog) {

                $id = $this->redirectId;
                if (!$this->isResourceExists($dialog, $this->paths, $id)) {
                    $dialog->say(['id' => $id])
                        ->error($this->resourceNotExists);
                    return $this->toSelfResource($dialog, null);
                }

                $error = $this->deleteResource($dialog, $this->paths, $id);

                $this->checkResult($dialog, $error);
                return $dialog->restart();
            })
            ->end();

    }

    /**
     * @param Stage $stage
     * @param string|int|null $id
     * @return Navigator
     */
    protected function doSave(Stage $stage, $id) : Navigator
    {
        return $stage->buildTalk()
            ->askConfirm($this->askConfirmSave)
            ->wait()
            ->hearing()
            ->isChoice(0, function(Dialog $dialog){
                return $dialog->restart();
            })
            ->isChoice(1, function(Dialog $dialog) use ($id) {
                $error = $this->saveResource($dialog, $this->paths, $id);
                $this->checkResult($dialog, $error);
                return $dialog->restart();
            })
            ->end();
    }


    public function toSelfResource(Dialog $dialog, $id) : Navigator
    {
        $to = new static($this->paths, $id);
        return $dialog->redirect->replaceTo($to, Redirect::NODE_LEVEL);
    }

    public function tellReturnr(Dialog $dialog, Context $resource) : void
    {
        if (!$resource instanceof ResourceDef) {
            return;
        }

        $code = $resource->errCode;
        if ($code == 0) {
            $dialog->say()->info($this->success);
            return;
        }
        $dialog->say()->info(static::ERR_MESSAGES[$code]);
    }

    protected function validatePaths(array $paths) : bool
    {
        if (empty(static::PATHS)) {
            return true;
        }

        if (empty($paths)) {
            return false;
        }

        $keys = static::PATHS;
        foreach ($keys as $key) {
            if (!isset($paths[$key]) || $paths[$key] === '') {
                return false;
            }
        }
        return true;
    }

    public static function makeContextName(
        string $module,
        array $pathNames,
        string $id
    ) : string
    {
        $paths = [$module];
        $paths = array_merge($paths, $pathNames);
        $paths[] = $id;
        return implode('.', $paths);
    }

    public static function getContextName(): string
    {
        return static::makeContextName(
            static::MODULE,
            static::PATHS,
            static::IDENTITY
        );
    }

    public function __getOffset() : int
    {
        return $this->limit * $this->pageIndex;
    }

    public function __getIsList() : bool
    {
        return !$this->isResource;
    }

    public function __getIsResource() : bool
    {
        $id = $this->id;
        return isset($id);
    }

    public function __getUri() : string
    {
        $domain = static::MODULE;

        $sections = [];
        foreach (static::PATHS as $name) {
            $value = $this->paths[$name] ?? '';
            $sections[] = "$name/$value";
        }
        $paths = implode('/', $sections);

        $idName = static::IDENTITY;
        $id = $this->id ?? '';
        return "$domain:$paths/$idName/$id";
    }

    public function __getPageIndex() : int
    {
        return $this->page > 0 ? $this->page - 1 : 0;
    }

    public function __exiting(Exiting $listener): void
    {
        $listener->onCancel(function(Dialog $dialog, Context $callback){
            if ($callback instanceof ResourceDef) {
                $this->tellReturnr($dialog, $callback);
            }
        });
    }

    public static function __depend(Depending $depending): void
    {
        foreach (static::PATHS as $path) {
            $depending->onEntity(new PathEtt($path));
        }
    }

    public function checkResult(Dialog $dialog, string $error = null) : void
    {
        if (is_null($error)) {
            $dialog->say()->info($this->success);
        } else {
            $dialog->say(['error' => $error])->error($this->failure);
        }

    }

    /**
     * @param array $paths
     * @param string $key
     * @param int|string|null $id
     * @return ResourceDef
     */
    public function newResource(array $paths, string $key, $id) : ResourceDef
    {
        $repo = $this->getSession()->contextRepo;

        $name = self::makeContextName(
            static::MODULE,
            array_keys($paths),
            $key
        );

        if (!$repo->hasDef($name)) {
            throw new ConfigureException(
                __METHOD__
                . ' composed context name ' . $name
                . ' not registered'
            );
        }

        $context = $repo->getDef($name)->newContext($paths, $id);

        if ($context instanceof ResourceDef) {
            return $context;
        }

        $type = is_object($context) ? get_class($context) : gettype($context);
        throw new ConfigureException(
            __METHOD__
            . ' can not make valid resource, '
            . " $type given"
        );
    }

    public function toSubList(Dialog $dialog, string $key) : Navigator
    {
        $paths = $this->paths;
        $paths[static::IDENTITY] = $this->id;
        $this->redirect = $this->newResource($paths, $key, null);
        return $dialog->goStage('redirect');
    }

    public function toSubResource(Dialog $dialog, string $key, $id) : Navigator
    {
        $paths = $this->paths;
        $paths[static::IDENTITY] = $this->id;
        $this->redirect = $this->newResource($paths, $key, $id);
        return $dialog->goStage('redirect');
    }

    public function toList(Dialog $dialog, array $paths, string $key) : Navigator
    {
        $this->redirect = $this->newResource($paths, $key, null);
        return $dialog->goStage('redirect');
    }

    public function toResource(Dialog $dialog, array $paths, string $key, $id) : Navigator
    {
        $this->redirect = $this->newResource($paths, $key, $id);
        return $dialog->goStage('redirect');
    }

    public function __onRedirect(Stage $stage) : Navigator
    {
        return $stage->dependOn($this->redirect, function(Dialog $dialog, Context $callback){
            $this->tellReturnr($dialog, $callback);
            return $dialog->restart();
        });

    }


}