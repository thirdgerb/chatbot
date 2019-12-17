<?php

namespace Commune\Chatbot\Framework\Impl;

use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Config\Children\TranslationConfig;
use Commune\Chatbot\Contracts\Translator as Contract;
use Symfony\Component\Translation\MessageCatalogue;
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

        // 空内容不校验.
        if (empty($id)) {
            return $id;
        }

        $params = [];
        // 过滤掉不合适翻译用的值.
        $marker = static::MARKER;
        foreach ($parameters as $key => $value) {
            if (is_scalar($value)) {
                $index = $marker . trim($key, $marker) . $marker;
                $params[$index] = $value;
            }
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

        // 默认使用 intl, 建议加载.
        if (extension_loaded('intl')) {
            $domain = $domain.MessageCatalogue::INTL_DOMAIN_SUFFIX;
        }

        $this->translator->addResource(
            $loader,
            $resource,
            $locale,
            $domain
        );
    }


}