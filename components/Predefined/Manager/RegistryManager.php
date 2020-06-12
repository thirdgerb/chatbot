<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined\Manager;

use Commune\Blueprint\Ghost\Auth\Supervise;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Components\Predefined\Intent\Navigation\CancelInt;
use Commune\Components\Predefined\Manager\OptRegistry\ViewCategory;
use Commune\Ghost\Context\ACodeContext;
use Commune\Protocals\HostMsg\Convo\QA\Choice;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Utils\ArrPaginator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title  配置注册表管理
 * @desc 配置注册表管理
 *
 * @property int $page
 * @property int $limit
 */
class RegistryManager extends ACodeContext
{

    public static function __name(): string
    {
        return 'commune.predefined.manager.registry';
    }

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([

            'auth' => [
                Supervise::class
            ],
            'memoryAttrs' => [
                'page' => 1,
                'limit' => 10
            ]
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog, OptRegistry $registry) {

                $paginator = $this->getPaginator($registry);
                $page = $this->page;
                $max = $paginator->maxPage();
                $menu = $this->getMenu($paginator, $page);

                return $dialog
                    ->send()
                    ->info("当前已注册的配置总数: {total}", ['total'=>$paginator->count()])
                    ->over()
                    ->await()
                    ->askChoose(
                        "选择配置项查看详情 ($page/$max) :",
                        $menu
                    );

            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isAnswered()
                    ->then(function(Dialog $dialog, Choice $isAnswered, OptRegistry $registry) {

                        $paginator = $this->getPaginator($registry);
                        $categories = $paginator->page($this->page);
                        $categories = array_values($categories);

                        $choice = $isAnswered->getChoice();


                        $category = is_numeric($choice)
                            ? ($categories[(int) $choice] ?? null)
                            : null;

                        if (isset($category)) {
                            return $dialog->blockTo(
                                ViewCategory::genUcl(['categoryName' => $category->name])
                            );

                        } elseif ($choice === 'n') {
                            $this->page = $this->page + 1;
                            return $dialog->reactivate();

                        } elseif ($choice === 'b') {
                            $this->page = $this->page - 1;
                            return $dialog->reactivate();

                        } else {
                            return $dialog
                                ->send()
                                ->error("invalid choice $choice")
                                ->over()->rewind();
                        }

                    })
                    ->end();

            });
    }

    protected function getMenu(ArrPaginator $paginator, int $page) : array
    {
        $categories = $paginator->page($page);
        $categories = array_values($categories);
        $max = $paginator->maxPage();

        $menu = array_map(function(CategoryOption $option) {
            return $option->title;
        }, $categories);

        if ($page < $max) {
            $menu['n'] = '下一页';
        }
        if ($page > 1) {
            $menu['b'] = '上一页';
        }

        $menu['c'] = CancelInt::genUcl();
        return $menu;
    }

    /**
     * @param OptRegistry $registry
     * @return ArrPaginator
     */
    protected function getPaginator(OptRegistry $registry) : ArrPaginator
    {
        return new ArrPaginator($registry->getCategoryOptions(), $this->limit);
    }

}