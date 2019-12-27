<?php


namespace Commune\Demo;


use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\Memories\TestUserInfoMemory;

/**
 * @property TestUserInfoMemory $user
 */
class TestMemoryB extends OOContext
{
    const DESCRIPTION = '测试用例B, 用 Memory 作为整个 Entity';

    public static function __depend(Depending $depending) : void
    {
        // 定义整个 Memory 对象作为一个 Entity
        $depending->onMemory('user', TestUserInfoMemory::class);
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info(
                '您的信息是, name:%name%; email:%email%;',
                [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ]
            )
            ->fulfill();
    }

}