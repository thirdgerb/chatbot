<?php


namespace Commune\Chatbot\App\Callables\StageComponents;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Callables\StageComponent;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Paginator implements StageComponent
{
    /**
     * @var string
     */
    public $introduce = '';

    /**
     * @var string
     */
    public $foot = '第%page%/%total%页, 输入数字序号进入指定页数';

    /**
     * @var string
     */
    public $question = '请输入: ';

    /**
     * @var int
     */
    public $limit = 30;


    /**
     * @var callable
     */
    protected $paginate;

    /**
     * @var callable
     */
    protected $listing;

    /**
     * @var int
     */
    protected $totalPage;

    /**
     * @var array   'index: desc' => callable $action
     */
    protected $menu;

    /**
     * @var null|callable
     */
    protected $fallback;

    /**
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

    public function withIntro(string $introduce) : self
    {
        $this->introduce = $introduce;
        return $this;
    }

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

            $dialog->say($slots)->info($this->introduce);

            $offset = $page * $this->limit;

            // read items
            $items = call_user_func_array(
                $this->paginate,
                [$self, $dialog, $offset, $this->limit]
            );

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

            // 执行菜单.
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

            // 判断输入是不是页码
            $hearing->isInstanceOf(VerboseMsg::class, function(Context $self, Dialog $dialog, VerboseMsg $msg){

                $text = $msg->getTrimmedText();
                if (!is_numeric($text)) {
                    return null;
                }

                $page = intval($text);
                $page = $page > 0 ? $page : 1;
                $self->page = $page - 1;

                return $dialog->repeat();
            });

            return $hearing->end($this->fallback);
        });
    }

}