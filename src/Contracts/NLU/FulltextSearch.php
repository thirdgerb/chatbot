<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\NLU;


/**
 * 全文搜索模块.
 * 使用全文搜索充当自然语言匹配.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface FulltextSearch
{
    public function search(string $searchText) : array; /* document id */

    public function index(string $docId, string $text) : bool;

    public function delete(string $search) : bool;

    public function indexAll(array $docIdToTextsMap) : bool;

}