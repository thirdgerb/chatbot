<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\NLU;


/**
 * 全文搜索工具.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface FulltextSearch extends NLUService
{
    public function search(string $query, int $limit) : array;

    public function searchBoolean(string $query, int $limit) : array;

    public function insert(array $data) : bool;

    public function update($docId, array $data) : bool;

    public function delete($docId) : bool;

}