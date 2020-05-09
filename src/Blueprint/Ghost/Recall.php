<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Ghost\Cloner\ClonerInstance;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recall extends \ArrayAccess, ArrayAndJsonAble, ClonerInstance
{

    /**
     * @param Cloner $cloner
     * @param string|null $id
     * @return static
     */
    public static function find(Cloner $cloner, string $id = null) : Recall;


    public function getId() : string;

    public function isLongTerm() : bool;

    public static function getName() : string;

    public static function getScopes() : array;

    public static function stub() : array;
}