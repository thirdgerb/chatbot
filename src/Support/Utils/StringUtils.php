<?php


namespace Commune\Support\Utils;


class StringUtils
{

    public static function couldBeString($value) : bool
    {
        return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }

    public static function normalizeContextName(string $name) : string
    {
        return strtolower(static::namespaceSlashToDot($name));
    }

    public static function namespaceSlashToDot(string $name) : string
    {
        return str_replace('\\', '.', $name);
    }

    public static function dotToNamespaceSlash(string $name) : string
    {
        return str_replace('.', '\\', $name);
    }

    public static function fetchDescAnnotation(string $docComment) : ? string
    {
        $matches = [];
        preg_match('/@description\s([^\*]+)/is', $docComment, $matches);
        return isset($matches[1]) ? trim($matches[1]) : null;
    }

    /**
     * @param string $docComment
     * @return array  [ [propertyName, propertyDesc], [name2, desc2]]
     */
    public static function fetchPropertyAnnotations(string $docComment) : array
    {
        $matches = [];
        preg_match_all(
            '/@property\s+[^\$]+\$(\w+)(.*)/',
            $docComment,
            $matches,
            PREG_SET_ORDER
        );

        return array_map(function(array $set){
            return [ $set[1] ?? '', trim($set[2] ?? '') ];
        }, $matches);
    }

    /**
     * @param string $docComment
     * @param string $marker
     * @return array [ [propertyName, type, desc], ]
     */
    public static function fetchPropertyAnnotationsDetails(string $docComment, string $marker = '@property') : array
    {
        $matches = [];
        $pattern = sprintf(
            '/%s([^\$]+)\$(\w+)(.*)/',
            $marker
        );
        preg_match_all(
            $pattern,
            $docComment,
            $matches,
            PREG_SET_ORDER
        );

        return array_map(function(array $set){
            return [
                $set[2] ?? '',
                trim($set[1] ?? ''),
                trim($set[3] ?? '')
            ];
        }, $matches);

    }


    public static function hasAnnotation(string $doc, string $annotation) : bool
    {
        $matched = preg_match('/@'. $annotation.'\s/', $doc);
        return is_int($matched) && $matched > 0;
    }

    public static function matchNameAndMethod(string $name) : ? array
    {
        $matches = [];
        preg_match(
            '/^([a-zA-Z][\w\.]*)@([a-zA-Z]\w*)$/',
            $name,
            $matches
        );

        if (count($matches) === 3 ) {
            return [$matches[1], $matches[2]];
        }
        return null;
    }

    public static function normalizeString(string $str) : string
    {
        return static::sbc2dbc(strtolower($str));
    }

    /**
     * 把全角符号做一些替换.
     * @see https://www.bbsmax.com/A/Ae5Ry6bAJQ/
     * @param string $str
     * @return string
     */
    public static function sbc2dbc(string $str) : string
    {
        $arr = array(
            '０'=>'0', '１'=>'1', '２'=>'2', '３'=>'3', '４'=>'4','５'=>'5', '６'=>'6', '７'=>'7', '８'=>'8', '９'=>'9',
            'Ａ'=>'A', 'Ｂ'=>'B', 'Ｃ'=>'C', 'Ｄ'=>'D', 'Ｅ'=>'E','Ｆ'=>'F', 'Ｇ'=>'G', 'Ｈ'=>'H', 'Ｉ'=>'I', 'Ｊ'=>'J',
            'Ｋ'=>'K', 'Ｌ'=>'L', 'Ｍ'=>'M', 'Ｎ'=>'N', 'Ｏ'=>'O','Ｐ'=>'P', 'Ｑ'=>'Q', 'Ｒ'=>'R', 'Ｓ'=>'S', 'Ｔ'=>'T',
            'Ｕ'=>'U', 'Ｖ'=>'V', 'Ｗ'=>'W', 'Ｘ'=>'X', 'Ｙ'=>'Y','Ｚ'=>'Z', 'ａ'=>'a', 'ｂ'=>'b', 'ｃ'=>'c', 'ｄ'=>'d',
            'ｅ'=>'e', 'ｆ'=>'f', 'ｇ'=>'g', 'ｈ'=>'h', 'ｉ'=>'i','ｊ'=>'j', 'ｋ'=>'k', 'ｌ'=>'l', 'ｍ'=>'m', 'ｎ'=>'n',
            'ｏ'=>'o', 'ｐ'=>'p', 'ｑ'=>'q', 'ｒ'=>'r', 'ｓ'=>'s', 'ｔ'=>'t', 'ｕ'=>'u', 'ｖ'=>'v', 'ｗ'=>'w', 'ｘ'=>'x',
            'ｙ'=>'y', 'ｚ'=>'z',
            '（'=>'(', '）'=>')', '〔'=>'(', '〕'=>')', '【'=>'[','】'=>']', '〖'=>'[', '〗'=>']', '“'=>'"', '”'=>'"',
            '‘'=>'\'', '｛'=>'{', '｝'=>'}', '《'=>'<','》'=>'>','％'=>'%', '＋'=>'+', '—'=>'-', '－'=>'-',
            '～'=>'~','：'=>':', '。'=>'.', '、'=>',', '，'=>',', '；'=>';', '？'=>'?', '！'=>'!', '…'=>'-',
            '‖'=>'|', '｜'=>'|', '〃'=>'"','　'=>' ', '×'=>'*', '￣'=>'~', '．'=>'.', '＊'=>'*',
            '＆'=>'&','＜'=>'<', '＞'=>'>', '＄'=>'$', '＠'=>'@', '＾'=>'^', '＿'=>'_', '＂'=>'"', '￥'=>'$', '＝'=>'=',
            '＼'=>'\\', '／'=>'/',
            '一' => '1', '二' => '2', '三' => '3', '四' => '4', '五' => '5', '六' => '6', '七' => '7', '八' => '8', '九' => '9', '零' => '0',

        );
        return strtr($str, $arr);
    }

    public static function simpleCharToInt(string $char) : ? int
    {
        $matcher = [
            '甲' => 1,
            '乙' => 2,
            '丙' => 3,
            '丁' => 4,
            '戊' => 5,
            '己' => 6,
            '庚' => 7,
            '辛' => 8,
            '壬' => 9,
            '葵' => 10,
            '子' => 1,
            '丑' => 2,
            '寅' => 3,
            '卯' => 4,
            '辰' => 5,
            '巳' => 6,
            '午' => 7,
            '未' => 8,
            '申' => 9,
            '酉' => 10,
            '戌' => 11,
            '亥' => 12,
            '零' => 0,
            '一' => 1,
            '二' => 2,
            '三' => 3,
            '四' => 4,
            '五' => 5,
            '六' => 6,
            '七' => 7,
            '八' => 8,
            '九' => 9,
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
            'e' => 5,
            'f' => 6,
            'g' => 7,
            'h' => 8,
            'i' => 9,
            'j' => 10,
            'k' => 11,
            'l' => 12,
            'm' => 13,
            'n' => 14,
            'o' => 15,
            'p' => 16,
            'q' => 17,
            'r' => 18,
            's' => 19,
            't' => 20,
            'u' => 21,
            'v' => 22,
            'w' => 23,
            'x' => 24,
            'y' => 25,
            'z' => 26,
            '最后' => -1,
            'last' => -1,
            'end' => -1,
            '倒数第一' => -1,
        ];

        return $matcher[$char] ?? null;
    }



    public static function validateDefName(string $contextName): bool
    {
        $name = StringUtils::normalizeContextName($contextName);
        $secs = explode('.', $name);
        foreach ($secs as $sec) {
            if (! preg_match('/^[a-z0-9]+$/', $sec)) {
                return false;
            }
        }
        return true;
    }


    public static function dotPathParser(string $current, string $target) : string
    {
        $start = $target[0] ?? '';
        if ($start !== '.') {
            return $target;
        }

        // i = 1, 同级目录
        // i = 2, 上级目录
        // i = 3, 上级的上级, 依此类推.
        for ($i = 0; $i < strlen($target); $i ++) {
            if ($target[$i] !== '.') {
                break;
            }
        }

        $secs = explode('.', $current);
        array_pop($secs);

        $lastPart = substr($target, $i);
        $parts = count($secs);

        if ($parts < ($i - 1)) {
            throw new \InvalidArgumentException("invalid path $target for current $current");
        }

        $sections = array_slice($secs, 0, $parts - ($i - 1));
        $middle = empty($secs) ? '' : implode('.', $sections);
        $middle = $middle ? "$middle." : '';

        return "$middle$lastPart";
    }
}