<?php

/**
 * Class SymfonyTranslator
 * @package Commune\Chatbot\Framework\Predefined
 */

namespace Commune\Chatbot\Framework\Predefined;

use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Config\Translation\TranslationConfig;
use Commune\Chatbot\Contracts\Translator as Contract;
use Symfony\Component\Translation\Translator as SymfonyTranslator;

/**
 * 基于 symfony translator 做翻译单元.
 */
class SymfonyTranslatorAdapter implements Contract
{

    /**
     * @var SymfonyTranslator
     */
    protected $translator;

    /**
     * @var TranslationConfig
     */
    protected $config;


    public function __construct(
        SymfonyTranslator $translator,
        TranslationConfig $config
    )
    {
        $this->translator = $translator;
        $this->config = $config;
    }

    public function trans(
        string $id,
        array $parameters = [],
        string $domain = null,
        string $locale = null
    ): string
    {
        $params = [];
        foreach($parameters as $key => $value) {
            $slotKey = self::MARKER . trim($key, self::MARKER) . self::MARKER;
            $params[$slotKey] = $value;
        }
        return $this->translator->trans($id, $params, $domain, $locale);
    }

    public function addResource(
        $resource,
        string $locale = null,
        string $domain = null,
        string $loader = null
    ): void
    {
        $locale = $locale ?? $this->config->defaultLocale;
        $domain = $domain ?? Translator::MESSAGE_DOMAIN;
        $loader = $loader ?? $this->config->loader;

        $this->translator->addResource(
            $loader,
            $resource,
            $locale,
            $domain
        );
    }


}