<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Markdown\Data;

use Commune\Support\ArrTree\Tree;
use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id                  文档的全局唯一ID. 通常根据 path 计算.
 * @property-read string $rootName            根节点名称.
 * @property-read int $updatedAt         最后更新时间.
 *
 * @property array $tree                 所有子节点序号的树状结构.
 *
 */
class MDDocumentData extends AbsOption
{
    const IDENTITY = 'id';

    /**
     * @var MDSectionData[]|null
     */
    protected $_sectionMap;

    public static function stub(): array
    {
        return [
            'id' => '',
            // 根节点名称.
            'rootName' => '',
            // 用序号呈现的树.
            // 通过 orderId 来获取所有的子节点.
            'tree' => [],
            'updatedAt' => time(),
        ];
    }

    public static function relations(): array
    {
        return [];
    }
}