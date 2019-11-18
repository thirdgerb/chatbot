<?php


namespace Commune\Chatbot\App\Callables\StageComponents;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\OOHost\Context\Callables\StageComponent;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 一个在 Stage 层实现分页的组件. 用于示范 stageComponent
 * 对象 Context 应该定义 int $page 属性, 用于存储当前页数.
 */
class Paginator implements StageComponent
{
    /**
     * 分页显示的介绍
     * @var string
     */
    public $introduce = '';

    /**
     * 分页结束后显示的内容.
     * @var string
     */
    public $foot = '第%page%/%total%页, 输入数字序号进入指定页数';

    /**
     * 要求用户输入指令时的内容.
     * @var string
     */
    public $question = '请输入: ';

    /**
     * 每页最大的数量.
     * @var int
     */
    public $limit = 30;


    /**
     * 分页方法
     * 会传入 (Context $self, Dialog $dialog, int $offset, int $limit)
     * 获取当前页的多个item
     *
     * @var callable
     */
    protected $paginate;

    /**
     * 用于展示 item 列表
     * 传入参数 (Context $self, Dialog $dialog, array $items)
     * @var callable
     */
    protected $listing;

    /**
     * 分页嘛就一定要知道总页数
     * @var int
     */
    protected $totalPage;

    /**
     * 定义分页页面上用户允许的其它操作.
     *
     * 用 "index : desc" 做key, 方便定义选项的描述.
     * @var array   'index: desc' => callable $action
     */
    protected $menu;

    /**
     * hearing 默认的 fallback
     * @var null|callable
     */
    protected $fallback;

    /**
     * hearing 可用的组件.
     * @var null|callable
     */
    protected $hearing;

    /**
     * Paginator constructor.
     * @param int $totalPage
     * @param callable $paginate
     * @param callable $listing
     * @param array $menu
     * @param callable|null $fallback
     * @param callable|null $hearing
     */
    public function __construct(
        int $totalPage,
        callable $paginate,
        callable $listing,
        array $menu = null,
        callable $fallback = null,
        callable $hearing = null
    )
    {
        $this->paginate = $paginate;
        $this->listing = $listing;
        $this->totalPage = $totalPage;
        $this->menu = $menu;
        $this->fallback = $fallback;
        $this->hearing = $hearing;
    }

    /**
     * 修改
     * @param string $introduce
     * @return Paginator
     */
    public function withIntro(string $introduce) : self
    {
        $this->introduce = $introduce;
        return $this;
    }

    /**
     * 设置每页最大数量.
     * @param int $limit
     * @return Paginator
     */
    public function withLimit(int $limit) : self
    {
        $this->limit = $limit;
        return $this;
    }

    public function withFoot(string $foot) : self
    {
        $this->foot = $foot;
        return $this;
    }



    public function __invoke(Stage $stage): Navigator
    {
        return $stage->talk(function(Context $self, Dialog $dialog) : Navigator {

            $menu = [];

            $keys = array_keys($this->menu);
            foreach ($keys as $key) {
                list($index, $desc) = explode(':', $key, 2);
                $menu[$index] = $desc;
            }

            $page = $self->page;

            $page = intval($page);
            $page = $page > 0 ? $page : 0;

            $slots = [
                'page' => $page + 1,
                'total' => $this->totalPage
            ];

            // 开头介绍.
            $dialog->say($slots)->info($this->introduce);

            $offset = $page * $this->limit;

            // 从分页方法中读取数据
            $items = call_user_func_array(
                $this->paginate,
                [$self, $dialog, $offset, $this->limit]
            );

            // 展示分页的数据.
            if (!empty($items)) {
                // list
                call_user_func_array(
                    $this->listing,
                    [$self, $dialog, $items]
                );

            } else {
                $dialog->say()->warning('empty!');
            }

            $dialog->say()
                ->withSlots($slots)
                ->info($this->foot)
                ->withContext($self)
                ->askChoose(
                    $this->question,
                    $menu
                );

            return $dialog->wait();

        }, function(Dialog $dialog, Message $message) : Navigator{

            $hearing = $dialog->hear($message);

            // 执行菜单逻辑.
            if (!empty($this->menu)) {
                $keys = array_keys($this->menu);
                foreach ($keys as $key) {
                    list($index, $desc) = explode(':', $key, 2);
                    $hearing->isChoice($index, $this->menu[$key]);
                }
            }

            // hearing 组件.
            if (isset($this->hearing)) {
                call_user_func($this->hearing, $hearing);
            }

            // 注册 hearing 的回调.
            if (isset($this->fallback)) {
                $hearing->fallback($this->fallback);
            }

            // 判断输入是不是页码
            $hearing->isInstanceOf(VerbalMsg::class, function(Context $self, Dialog $dialog, VerbalMsg $msg){

                $text = $msg->getTrimmedText();
                if (!is_numeric($text)) {
                    return null;
                }

                $page = intval($text);
                $page = $page > 0 ? $page : 1;
                $self->page = $page - 1;

                return $dialog->repeat();
            });

            return $hearing->end();
        });
    }

}