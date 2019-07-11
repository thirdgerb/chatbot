<?php


namespace Commune\Chatbot\OOHost\Context;


/**
 * 占位符. 最低优, 可以随时覆盖.
 *
 * 因为一部分 context 可以对别的 context 进行操作.
 * 而别的 context 可能还没定义. 这会影响前者的功能, 形成依赖.
 *
 * 所以逻辑里可以先生成占位符.
 */
interface PlaceholderDefinition extends Definition
{
}