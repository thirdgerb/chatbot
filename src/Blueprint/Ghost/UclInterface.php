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

use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\Exceptions\InvalidQueryException;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


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
interface UclInterface extends ArrayAndJsonAble
{

    /*------ create ------*/

    /**
     * @param string $contextName
     * @param string $stageName
     * @param array $query
     * @return Ucl
     */
    public static function make(
        string $contextName,
        string $stageName,
        array $query
    ) : Ucl;

    /**
     * @param string $contextName
     * @param array $query
     * @return Ucl
     */
    public static function context(
        string $contextName,
        array $query
    ) : Ucl;

    /**
     * @param Cloner $cloner
     * @param string $contextName
     * @param array|null $query
     * @param string $stageName
     * @return Ucl
     * @throws InvalidQueryException
     * @throws DefNotDefinedException
     */
    public static function create(
        Cloner $cloner,
        string $contextName,
        array $query = null,
        string $stageName = ''
    )  : Ucl;

    /*------ property ------*/

    /**
     * @return string
     */
    public function getContextId() : string;

    /*------ compare ------*/

    /**
     * @return bool
     */
    public function isInstanced() : bool;

    /**
     * @param string $ucl
     * @return bool
     */
    public function atSameContext(string $ucl) : bool;

    /**
     * @param string $ucl
     * @return bool
     */
    public function isSameContext(string $ucl) : bool;

    /**
     * @param string $ucl
     * @return bool
     */
    public function equals(string $ucl) : bool;

    /*------ transformer ------*/

    /**
     * @param Cloner $cloner
     * @return Ucl
     * @throws InvalidQueryException
     * @throws DefNotDefinedException
     */
    public function toInstanced(Cloner $cloner) : Ucl;

    /**
     * @return string
     */
    public function toEncodedStr() : string;

    /**
     * @param string|null $stage
     * @return string
     */
    public function toIntentName(string $stage = null) : string;

    /**
     * @param string|null $stage
     * @return string
     */
    public function toFullStageName(string $stage = null) : string;


    /*------ redirect ------*/

    /**
     * @param string $stageName
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public function goStage(string $stageName) : Ucl;

    /**
     * @param string $fullStageName
     * @return Ucl
     */
    public function goFullnameStage(string $fullStageName) : Ucl;

    /*------ validate ------*/

    /**
     * @return bool
     */
    public function isValidPattern() : bool;

    /**
     * @param Cloner $cloner
     * @return bool
     */
    public function isValid(Cloner $cloner) : bool;

    /*------ encode decode ------*/

    /**
     * @param string|Ucl $string
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public static function decodeUclStr($string) : Ucl;

    /**
     * @param string $contextName
     * @param string $stageName
     * @param array $query
     * @return string
     */
    public static function encodeUcl(
        string $contextName,
        string $stageName = '',
        array $query = []
    ) : string;

    /**
     * @param array $query
     * @return string
     */
    public static function encodeQueryStr(array $query) : string;

    /**
     * @param string $str
     * @return array
     */
    public static function decodeQueryStr(string $str) : array;

    /*------ mindset ------*/

    /**
     * @param Cloner $cloner
     * @return bool
     */
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

    /**
     * @param Cloner $cloner
     * @return Context
     * @throws DefNotDefinedException
     * @throws InvalidArgumentException
     */
    public function findContext(Cloner $cloner) : Context;

    /*------ string ------*/

    /**
     * @return string
     */
    public function __toString() : string;
}
