<?php


namespace Commune\Support\Utils;


class StringUtils
{
    const REGEX_CHAR_REPLACE = [
        '.' => '\.',
        '?' => '\?',
        '+' => '\+',
        '$' => '\$',
        '^' => '\^',
        '[' => '\[',
        ']' => '\]',
        '(' => '\(',
        ')' => '\)',
        '{' => '\{',
        '}' => '\}',
        '|' => '\|',
        '\\' => "\\\\",
        '/' => '\/',
    ];

    public static function isStrGreaterThen(string $first, string $second) : bool
    {
        $firstLen = strlen($first);
        $second = strlen($second);

        if ($firstLen === $second) {
            return $first > $second;
        }

        return $firstLen > $second;
    }

    public static function gluePath(string $dir, string $more) : string
    {
        return rtrim($dir, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . ltrim($more, DIRECTORY_SEPARATOR);
    }

    public static function isEmptyStr($str) : bool
    {
        return self::isString($str) && (strval($str) === '');
    }

    public static function isString($str) : bool
    {
        return is_string($str)
            || (
                is_object($str)
                && method_exists( $str, '__toString')
            );
    }

    public static function isNotEmptyStr($str) : bool
    {
        return self::isString($str) && (strval($str) !== '');
    }

    /**
     * @param string $string
     * @param string $separator
     * @return array
     */
    public static function dividePrefixAndName(string $string, string $separator = '.') : array
    {
        $len = strlen($string);

        $last = '';
        for($i = ($len - 1) ; $i >= 0 ; $i --) {

            // 到了标记
            if ($string[$i] === $separator) {
                return [substr($string, 0, $i), $last];
            }

            $last = $string[$i] . $last;
        }

        return ['', $last];
    }

    /**
     * @param string $prefix
     * @param string $id
     * @param string $separator
     * @return string
     */
    public static function gluePrefixAndName(string $prefix, string $id, string $separator = '.') : string
    {
        $prefix = trim($prefix, $separator);

        if (empty($id)) {
            return $prefix;
        }
        return "$prefix$separator$id";
    }

    /**
     * 字符串是否包含通配符
     *
     * @param string $string
     * @return bool
     */
    public static function isWildcardPattern(string $string) : bool
    {
        return mb_strpos($string, '*') !== false;
    }

    /**
     * 通配符专为正则. 默认通配符只能匹配 \w
     *
     * @param string $string
     * @param string $replace
     * @return string
     */
    public static function wildcardToRegex(string $string, string $replace = '.+') : string
    {
        $string = strtr($string, self::REGEX_CHAR_REPLACE);
        $string = str_replace('*', $replace, $string);
        return "/^$string$/";
    }

    public static function wildcardSearch(string $wildcardId, array $ids, string $replace = '.*') : array
    {
        if ($wildcardId === '*') {
            return $ids;
        }

        if (self::isWildcardPattern($wildcardId)) {
            $pattern = self::wildcardToRegex($wildcardId, $replace);
            return array_filter($ids, function($id) use ($pattern) {
                return (bool) preg_match($pattern, $id);
            });
        }

        return in_array($wildcardId, $ids) ? [$wildcardId] : [];
    }

    public static function wildcardMatch(string $wildcardId, string $actual, string $replace = '.+') : bool
    {
        $pattern = self::wildcardToRegex($wildcardId, $replace);
        return (bool) preg_match($pattern, $actual);
    }

    /**
     * 字符串去掉默认的符号
     *
     * @param string $str
     * @return string
     */
    public static function trim(string $str) : string
    {
        return trim($str, " \t\n\r\0\x0B.,;");
    }

    /**
     * 对象是否可以转化为字符串
     * @param mixed $value
     * @return bool
     */
    public static function couldBeString($value) : bool
    {
        return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }


    /**
     * 命名空间 \ 转为用 . 连接的字符串.
     * @param string $name
     * @return string
     */
    public static function namespaceSlashToDot(string $name) : string
    {
        return str_replace('\\', '.', $name);
    }

    /**
     * 用 . 连接的字符串专为类名
     * @param string $name
     * @return string
     */
    public static function dotToNamespaceSlash(string $name) : string
    {
        return str_replace('.', '\\', $name);
    }

    /**
     * 获取 description 注解
     * @param string $docComment
     * @return null|string
     */
    public static function fetchDescAnnotation(string $docComment) : ? string
    {
        $matches = self::fetchAnnotation($docComment, 'description');
        return $matches[0] ?? null;
    }

    public static function fetchAnnotation(string $docComment, string $annotation) : array
    {
        $matches = [];
        $pattern = sprintf('/@%s\s([^\*]+)/', $annotation);
        preg_match_all($pattern, $docComment, $matches);

        return array_map(
            function($matched){
                return self::trim($matched);
            },
            $matches[1] ?? []
        );
    }

    /**
     * 获取 property 注解
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
     * 把严格的 property 类注解拆分成 name, type, desc
     *
     * @param string $docComment
     * @param string $prefix       @"param"|@"property" 等注解.
     * @param bool $noSuffix       是否允许有后缀
     * @return array [ [propertyName, type, desc], ]
     */
    public static function fetchVariableAnnotationsWithType(string $docComment, string $prefix = '@property', bool $noSuffix = false) : array
    {
        $matches = [];
        $suffix = $noSuffix ? '' : '[a-zA-Z-]*';
        $pattern = sprintf(
            '/%s%s\s+([^\$]*)\$(\w+)([^\n]*)/',
            $prefix,
            $suffix
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

    public static function separateAnnotation(string $comment) : ? array
    {
        $comment = trim($comment);
        preg_match('/^@([a-zA-Z]+)(\s+.+){0,1}$/', $comment, $matches);
        if (empty($matches)) {
            return null;
        }

        return [
            $name = trim($matches[1]),
            $content = trim($matches[2] ?? ''),
        ];
    }

    /**
     * 是否包含某个注解
     *
     * @param string $doc
     * @param string $annotation
     * @return bool
     */
    public static function hasAnnotation(string $doc, string $annotation) : bool
    {
        return (bool) preg_match('/@'. $annotation.'\s/', $doc);
    }

    /**
     * 把 className@ method 转化为 [className, method]
     * @param string $name
     * @return array|null
     */
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

    /**
     * 标准化字符串, 包括转化全角, 和大小写一致
     * @param string $str
     * @return string
     */
    public static function normalizeString(string $str) : string
    {
        $str = self::trim($str);
        $str = strtolower($str);
        return static::sbc2dbc($str);
    }

    /**
     * 把全角符号替换成一般字符.
     *
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
            '～'=>'~','：'=>':', '.'=>'.', '、'=>',', '，'=>',', '；'=>';', '？'=>'?', '！'=>'!', '…'=>'-',
            '‖'=>'|', '｜'=>'|', '〃'=>'"','　'=>' ', '×'=>'*', '￣'=>'~', '．'=>'.', '＊'=>'*',
            '＆'=>'&','＜'=>'<', '＞'=>'>', '＄'=>'$', '＠'=>'@', '＾'=>'^', '＿'=>'_', '＂'=>'"', '￥'=>'$', '＝'=>'=',
            '＼'=>'\\', '／'=>'/',
            '一' => '1', '二' => '2', '三' => '3', '四' => '4', '五' => '5', '六' => '6', '七' => '7', '八' => '8', '九' => '9', '零' => '0',

        );
        return strtr($str, $arr);
    }

    /**
     * 将一部分序数字符转化为整形
     * @param string $char
     * @return int|null
     */
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
        $secs = explode('.', $contextName);
        foreach ($secs as $sec) {
            if (! preg_match('/^[a-z0-9\-]+$/', $sec)) {
                return false;
            }
        }
        return true;
    }


    /**
     * 将用 . 分割开来的相对路径, 转化为绝对路径.
     *
     * 例如:
     *  .xxx        同级目录
     *  ..xxx       上级目录
     *  ...xxx      上N级目录
     *
     * @param string $current
     * @param string $target
     * @return string
     */
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

    public static function expectKeywords(string $text, array $keywords, bool $all = true) : bool
    {
        if (empty($keywords)) {
            return false;
        }

        if (empty($text)) {
            return false;
        }

        foreach ($keywords as $keyword) {

            if (is_array($keyword)) {
                // 只要存在一个. 同义词.
                $matched = self::expectKeywords($text, $keyword, !$all);

            } else {
                // 判断关键字是否存在.
                $matched = is_int(mb_strpos($text, $keyword));
            }

            if (!$all && $matched) {
                return true;
            }

            if ($all && !$matched) {
                return false;
            }
        }

        return $all;
    }

    public static function isRegexPattern(string $text) : bool
    {
        $len = strlen($text);
        return $len > 2 && $text[0] === '/' && $text[$len - 1] === '/';
    }

    public static function isValidDotDirName(string $text) : bool
    {
        return (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $text) > 0)
            && (false === strstr($text, '..'));
    }

}