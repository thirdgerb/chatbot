<?php


namespace Commune\Chatbot\Framework\Utils;


class StringUtils
{

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
            '/@property[^\$]+\$(\w+)(.*)/',
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
}