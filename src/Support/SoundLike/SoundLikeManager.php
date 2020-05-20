<?php

/**
 * Class SoundLikeManager
 * @package Commune\Support\SoundLike
 */

namespace Commune\Support\SoundLike;


use Commune\Support\Utils\StringUtils;
use Psr\Container\ContainerInterface;

class SoundLikeManager implements SoundLikeInterface
{
    /**
     * @var SoundLikeParser[]
     */
    protected $parsers = [];

    /**
     * @var string[]
     */
    protected $abstracts = [];

    /**
     * @var ContainerInterface
     */
    protected $container;


    /**
     * SoundLikeManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function register(string $lang, string $parserAbstract) : void
    {
        $this->abstracts[$lang] = $parserAbstract;
    }

    public function registerParser(string $lang, SoundLikeParser $parser): void
    {
        $this->parsers[$lang] = $parser;
    }

    public function soundLike(
        string $input,
        string $expect,
        int $compareType = self::COMPARE_EXACTLY,
        string $lang = self::ZH
    ): bool
    {
        if (empty($input) || empty($expect)) {
            return false;
        }

        $input = StringUtils::normalizeString($input);
        $expect = StringUtils::normalizeString($expect);


        $parser = $this->getParser($lang);
        if (!isset($parser)) {
            return false;
        }
        return $parser->soundLike($input, $expect, $compareType);
    }

    protected function getParser(string $lang) : ? SoundLikeParser
    {
        if (isset($this->parsers[$lang])) {
            return $this->parsers[$lang];
        }

        $abstract = $this->abstracts[$lang] ?? null;
        if (isset($abstract)) {
            return $this->parsers[$lang] = $this->container->get($abstract);
        }

        return null;
    }


}