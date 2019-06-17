<?php


namespace Commune\Chatbot\App\Components\Configurable\Controllers;


use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\App\Components\Configurable\Drivers\DomainConfigRepository;

/**
 * @property int $page
 */
class AllDomainController extends Controller
{
    const DESCRIPTION = '查看所有模块 (domains)';

    protected $limit = 20;

    public function __construct()
    {
        parent::__construct([
            'page' => 1
        ]);
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->onStart(function(Dialog $dialog, DomainConfigRepository $repo){
            $count = $repo->getDomainCount();
            $pages = (int)ceil($count/$this->limit);
            $say = $dialog->say();
            $page = $this->page - 1;
            $say->info("共加载了 $count 个模块. 第 {$this->page}/$pages 页");

            $page = $page >= 0 ? $page : 0;
            $offset = $this->limit * $page;

            $names = $repo->paginateDomainNames($this->limit, $offset);

            if (empty($names)) {
                $say->warning("没有可显示的模块");
            } else {
                foreach ($names as $name) {
                    $domain = $repo->get($name);
                    $say->info("{$domain->domain} : {$domain->desc}");
                }
            }

        })->component(new Menu(
            '您的操作:',
            [
                '下一页' => function(Dialog $dialog){
                    $this->page +=1;
                    return $dialog->restart();

                },
                '第N页' => function(Dialog $dialog) {
                    return $dialog->goStage('page');
                },
                EditDomainController::class
            ]
        ));
    }

    public function __onPage(Stage $stage) : Navigator
    {
        return $stage->build()
            ->askVerbose('请输入第几页:')
            ->callback()
            ->action(function(Dialog $dialog, Message $message){
                $num = intval($message->getTrimmedText());

                if ($num > 0 ) {
                    $this->page = $num;
                    return $dialog->restart();
                }

                $dialog->say()
                    ->error('输入的页数不正确');

                return $dialog->repeat();
            });

    }

}