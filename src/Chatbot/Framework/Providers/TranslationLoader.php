<?php


namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\Config\ChatbotConfig;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Commune\Container\ContainerContract;

trait TranslationLoader
{
    /**
     * @var ContainerContract
     */
    protected $app;

    /**
     * @param ContainerContract $app
     */
    public function loading($app): void
    {
        $logger = $this->getLogger($app);
        $loader = $this->getLoader($app);

        // 根据配置, 确定loader和文件的后缀.
        $loader = in_array($loader, ['php', 'yaml', 'json', 'csv', 'xliff'])
            ? $loader
            : 'php';
        $filePattern = '/\.'.$loader.'$/';

        /**
         * 获取translator 实例. 通常是在reactor 环节就获取.
         * @var SymfonyTranslator $translator
         */
        $translator = $app->make(SymfonyTranslator::class);

        // 遍历翻译文件所在目录.
        $dirFinder = new Finder();
        $generator = $dirFinder->directories()
            ->in($this->getResourcePath($app))
            ->depth(0);

        foreach($generator as $dir) {
            /**
             *
             * @var SplFileInfo $dir
             */
            $locale = $dir->getBasename();
            // 认为文件夹名字就是地域命名.

            // 遍历该文件夹下所有文件, 不递归查找.
            $fileFinder = new Finder();
            foreach(
                $fileFinder->files()
                    ->in($dir->getPathname())
                    ->depth(0)
                    ->name($filePattern)
                as $file
            ) {
                // 认为文件名就是domain
                /**
                 * @var SplFileInfo $file
                 */
                $domain = str_replace('.'.$file->getExtension(), '', $file->getBasename());
                $path = $file->getPathname();

                // 记录日志
                $logger->debug("register trans resource, locale: $locale, domain:$domain, path:$path");

                // 加载资源成功.
                $translator->addResource(
                    $loader,
                    $path,
                    $locale,
                    $domain
                );
            }
        }
    }

    /**
     * @param ContainerContract $app
     * @return string
     */
    protected function getResourcePath($app) : string
    {
        /**
         * 获取翻译相关的配置.
         * @var ChatbotConfig $chatbotConfig
         */
        $chatbotConfig = $app->make(ChatbotConfig::class);
        return $chatbotConfig->translation->resourcesPath;
    }

    /**
     * @param ContainerContract $app
     * @return LoggerInterface
     */
    protected function getLogger($app) : LoggerInterface
    {
        return $app->make(LoggerInterface::class);
    }

    /**
     * @param ContainerContract $app
     * @return string
     */
    protected function getLoader($app) : string
    {
        /**
         * 获取翻译相关的配置.
         * @var ChatbotConfig $chatbotConfig
         */
        $chatbotConfig = $app->make(ChatbotConfig::class);
        $config = $chatbotConfig->translation;
        return $config->loader;
    }


}