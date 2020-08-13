<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Providers;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Components\Markdown\Mindset\MDContextDef;
use Commune\Components\Markdown\Options\MDGroupOption;
use Commune\Components\Markdown\Parsers\MD2ContextParser;
use Commune\Components\Tree\Prototype\TreeContextDef;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Markdown\Data\MDDocumentData;
use Commune\Support\Markdown\Data\MDSectionData;
use Commune\Support\Markdown\Parser\MDParser;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Utils\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id
 * @property-read bool $forceUpdate
 * @property-read string $resourcePath
 * @property-read MDGroupOption $group
 */
class MDGroupContextLoader extends ServiceProvider
{
    const IDENTITY = 'id';

    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public static function stub(): array
    {
        return [
            'id' => '',
            'forceUpdate' => false,
            'resourcePath' => '',
            'group' => [

            ],
        ];
    }

    public static function relations(): array
    {
        return [
            'group' => MDGroupOption::class
        ];
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var ConsoleLogger $logger
         * @var OptRegistry $registry
         */
        $group = $this->group;
        $logger = $app->make(ConsoleLogger::class);
        $registry = $app->make(OptRegistry::class);
        $mindset = $app->make(Mindset::class);

        $path = StringUtils::gluePath(
            $this->resourcePath,
            $group->relativePath
        );
        $path = realpath($path);

        $finder = new Finder();

        $gen = $finder
            ->files()
            ->in($path)
            ->name('*.md');

        $i = 0;
        foreach ($gen as $file) {
            $i ++ ;
            /**
             * @var \SplFileInfo $file
             */
            $filePath = $file->getRealPath();
            $markdownId = $this->getMarkdownId($path, $group, $filePath);

            $update = $this->forceUpdate
                || $this->checkShouldUpdate($markdownId, $file->getMTime(), $registry);

            if ($update) {
                $this->updateMarkdown(
                    $app,
                    $registry,
                    $mindset,
                    $logger,
                    $markdownId,
                    $filePath,
                    $group
                );
            } else {
                $logger->debug(
                    "markdown : skipped saved file $filePath id $markdownId"
                );
            }
        }

        if (empty($i)) {
            $logger->warning(
                __METHOD__
                . " no markdown file in path $path"
            );
        }
    }

    public static function updateMarkdown(
        ContainerContract $app,
        OptRegistry $registry,
        Mindset $mindset,
        LoggerInterface $logger,
        string $markdownId,
        string $filePath,
        MDGroupOption $group
    ) : void
    {
        $parserName = $group->markdownParser;
        if (!is_a($parserName, MDParser::class, true)) {
            throw new InvalidArgumentException("parser should be subclass of " . MDParser::class . ", $parserName given");
        }

        $content = file_get_contents($filePath);

        /**
         * @var MDParser $parser
         */
        $parser = call_user_func(
            [$parserName, MDParser::FUNC_PARSE],
            $markdownId,
            TreeContextDef::FIRST_STAGE,
            $content,
            // 将静态的注解从原来的正文中抽离出来.
            $staticComments = $group->getStaticComments()
        );

        $doc = $parser->getDocument();

        // 保存 doc
        $docCate = $registry->getCategory(MDDocumentData::class);
        // 强行覆盖
        $docCate->save($doc, false);

        // 保存段落.
        $sections = $parser->getSections();
        $secCate = $registry->getCategory(
            MDSectionData::class
        );
        foreach ($sections as $section) {
            $secCate->save($section, false);
        }

        $logger->debug(
            "markdown: save $filePath to OptRegistry, id $markdownId"
        );

        /**
         * @var MD2ContextParser $contextParser
         */
        $contextParser = $app->make($group->contextParser);
        $contextDef = $contextParser->parse($group, $parser);

        if ($contextDef instanceof MDContextDef) {

            // 为了防止重复数据浪费资源, 所以主动拿出来 stage 的定义.
            $stageMetas = $contextDef->stages;
            $contextDef->stages = [];

            // 强制注册所有的.
            $mindset->contextReg()->registerDef($contextDef, false);
            $stageReg = $mindset->stageReg();
            foreach ($stageMetas as $meta) {
                $stageReg->registerDef($meta->toWrapper(), false);
            }

        } else {
            $mindset->contextReg()->registerDef($contextDef, false);
        }
    }

    protected function checkShouldUpdate(
        string $markdownId,
        int $fileModTime,
        OptRegistry $registry
    ) : bool
    {
        $category = $registry->getCategory(MDDocumentData::class);

        if (!$category->has($markdownId)) {
            return true;
        }

        /**
         * @var MDDocumentData $option
         */
        $option = $category->find($markdownId);
        return $fileModTime - $option->updatedAt > 0;
    }

    protected function getMarkdownId(
        string $path,
        MDGroupOption $group,
        string $filePath
    ) : string
    {
        $relativePath = str_replace(
            [
                $path,
                '.md'
            ],
            '',
            $filePath
        );
        $relativeName = trim($relativePath, DIRECTORY_SEPARATOR);
        $relativeName = str_replace(
            DIRECTORY_SEPARATOR,
            '.',
            $relativeName
        );

        return trim($group->namespace, '.')
            . "."
            . $relativeName;
    }

    public function register(ContainerContract $app): void
    {
    }


}