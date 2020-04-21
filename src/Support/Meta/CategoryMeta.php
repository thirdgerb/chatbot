<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Meta;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id            分类的ID. 可能同一个 MetaType 拥有多个分类
 *
 * @property-read string $metaType      元数据的类名
 *
 * @property-read MetaHolder $rootStorage
 * 根 storage. 所有 storage 节点的数据以它为准.
 *
 * @property-read MetaHolder[] $storagePipeline
 * 获取数据的中间层.
 * 读取数据时, 从上往下读, 读到任何合法数据则返回.
 * 更新数据时数据从根节点往上同步.
 */
class CategoryMeta extends AbsOption
{

}