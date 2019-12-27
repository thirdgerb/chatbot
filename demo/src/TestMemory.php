<?php

namespace Commune\Demo;

use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\Memories\TestUserInfoMemory;

/**
 * @property-read TestUserInfoMemory $user
 */
class TestMemory extends OOContext
{
    const DESCRIPTION = '测试记忆';

    // 定义一个缓存, 避免频繁获取
    // 这个值在 Context 序列化时不会被存储.
    protected $_user;

    public function __onStart(Stage $stage): Navigator
    {
        $menu = new Menu(
            '请选择测试用例',
            [
                TestMemoryA::class,
                TestMemoryB::class,
                '测试用 getter 查看Memory' => 'getter',
            ]
        );

        return $stage->component($menu);
    }

    public function __onGetter(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info(
                '当前用户的信息为, name:%name%; email:%email%',
                [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ]
            )
            ->goStage('start');
    }


    public function __getUser() : TestUserInfoMemory
    {
        return $this->_user ?? $this->_user = TestUserInfoMemory::from($this);
    }
}