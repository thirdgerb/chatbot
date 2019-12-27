<?php

namespace Commune\Demo\Memories;

use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * 定义上下文, 默认直接通过注解定义 Memory 的 Entity
 *
 * @property string $name 请问如何称呼您
 * @property string $email 请问您的邮箱是?
 */
class TestUserInfoMemory extends MemoryDef
{
    // 定义上下文记忆的名称
    const DESCRIPTION = '模拟的用户信息记忆';

    // 定义上下文记忆的作用域维度
    // 记录的维度会决定上下文的 ID, 没有记录的维度都视作 0
    const SCOPE_TYPES = [
        // 表示用户 ID 维度
        Scope::USER_ID,
        // 表示 机器人维度
        Scope::CHATBOT_NAME,
    ];
}