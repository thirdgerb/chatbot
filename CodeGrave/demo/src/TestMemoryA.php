<?php


namespace Commune\Demo;


use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\Memories\TestUserInfoMemory;

/**
 * @property string $name
 */
class TestMemoryA extends OOContext
{
    const DESCRIPTION = '测试用例A, 用 Memory 的单个值作为 Entity';


    public static function __depend(Depending $depending) : void
    {
        // 指定 Memory 对象的一个值作为 Entity
        $depending->onMemoryVal('name', TestUserInfoMemory::class, 'name');
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info(
                '您的信息是, name:%name%;',
                [
                    'name' => $this->name,
                ]
            )
            ->fulfill();
    }
}