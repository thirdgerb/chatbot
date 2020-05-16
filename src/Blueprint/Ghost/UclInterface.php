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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;


/**
 * 把核心方法从 Ucl 中拆出来, 当成独立文档.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read string[] $query
 */
interface UclInterface
{


    /*------ create ------*/

    public static function create(
        Cloner $cloner,
        string $contextName,
        array $query = null,
        string $stageName = ''
    )  : Ucl;

    /*------ property ------*/

    public function getContextId() : string;

    /*------ compare ------*/

    public function atSameContext(string $ucl) : bool;

    /**
     * @param string $ucl
     * @return bool
     */
    public function isSameContext(string $ucl) : bool;

    public function equals(string $ucl);

    /*------ transformer ------*/

    public function toEncodedUcl() : string;

    public function toIntentName(string $stage = null) : string;

    public function toFullStageName(string $stage = null) : string;


    /*------ redirect ------*/

    /**
     * @param string $stageName
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public function goStage(string $stageName) : Ucl;

    public function goFullnameStage(string $fullStageName) : Ucl;

    /*------ validate ------*/


    public function isValid() : bool;


    /*------ encode decode ------*/

    /**
     * @param string|Ucl $string
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public static function decodeUcl($string) : Ucl;

    public static function encodeUcl(
        string $contextName,
        string $stageName = '',
        array $query = []
    ) : string;

    public static function encodeQueryStr(array $query) : string;

    public static function decodeQueryStr(string $str) : array;

    /*------ mindset ------*/

    public function stageExists(Cloner $cloner) : bool;

    /**
     * @param Cloner $cloner
     * @return StageDef
     * @throws DefNotDefinedException
     */
    public function findStageDef(Cloner $cloner) : StageDef;

    /**
     * @param Cloner $cloner
     * @return ContextDef
     * @throws DefNotDefinedException
     */
    public function findContextDef(Cloner $cloner) : ContextDef;

    /**
     * @param Cloner $cloner
     * @return IntentDef|null
     */
    public function findIntentDef(Cloner $cloner) : ? IntentDef;


    /*------ string ------*/

    public function __toString() : string;
}
