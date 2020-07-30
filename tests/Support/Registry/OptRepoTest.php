<?php


namespace Commune\Test\Support\OptionRepo;


use Commune\Framework\Log\IConsoleLogger;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Commune\Container\Container;
use Commune\Support\Registry\Demo\Demo;
use Commune\Support\Registry\Demo\TestOption;
use Commune\Support\Registry\Exceptions\OptionNotFoundException;
use Commune\Support\Registry\Impl\IOptRegistry;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonFileStorageDriver;
use Commune\Support\Registry\Storage\Json\JsonStorageOption;
use Commune\Support\Registry\Storage\PHP\PHPFileStorageDriver;
use Commune\Support\Registry\Storage\PHP\PHPStorageOption;
use Commune\Support\Registry\Storage\Yaml\YmlFileStorageDriver;
use Commune\Support\Registry\Storage\Yaml\YmlStorageOption;


class OptRepoTest extends TestCase
{
    protected function prepareContainer(CategoryOption $meta) : Container
    {
        $container = new Container();
        $container->instance(ContainerInterface::class, $container);
        $container->instance(LoggerInterface::class, new IConsoleLogger());

        $container->singleton(OptRegistry::class, IOptRegistry::class);

        $container->singleton(JsonFileStorageDriver::class);
        $container->singleton(PHPFileStorageDriver::class);
        $container->singleton(YmlFileStorageDriver::class);

        /**
         * @var OptRegistry $repo
         */
        $repo = $container->get(OptRegistry::class);
        $repo->registerCategory($meta);

        return $container;
    }

    protected function prepareMetas(string $metaId, string $optionName, bool $isDir = false) : CategoryOption
    {
        $data = [
            'name' => $metaId,
            'optionClass' => $optionName,
            'storage' => [],
            'initialStorage' => null,
        ];

        $wrappers = [
            'json' => JsonStorageOption::class,
            'yml' => YmlStorageOption::class,
            'php' => PHPStorageOption::class
        ];

        $path = [
            'json' => Demo::JSON,
            'yml' => Demo::YAML,
            'php' => Demo::PHP,
        ];

        $ext = $isDir ? '/data/' : "/data.$metaId";

        $storage = [
            'name' => $metaId,
            'wrapper' => $wrappers[$metaId],
            'config' => [
                'name' => $metaId,
                'path' => $path[$metaId] . $ext,
                'isDir' => $isDir,
            ]
        ];

        $data['storage'] = $storage;
        return new CategoryOption($data);
    }

    public function testRegistrySingleFile()
    {
        foreach (['json', 'yml', 'php'] as $type) {
            $meta = $this->prepareMetas($type, TestOption::class, false);
            $c = $this->prepareContainer($meta);
            $this->typeTest($c, $type);
        }
    }


    public function testRegistryDir()
    {
        foreach (['json', 'yml', 'php'] as $type) {
            $meta = $this->prepareMetas($type, TestOption::class, true);
            $c = $this->prepareContainer($meta);
            $this->typeTest($c, $type);
        }
    }

    protected function typeTest(Container $c, string $type)
    {
        /**
         * @var OptRegistry $repo
         */
        $repo = $c->get(OptRegistry::class);

        $category = $repo->getCategory($type);
        $category->save($expect = TestOption::createById('test'));

        $t = $category->find('test' );

        $this->assertTrue(isset($t));
        $this->assertEquals($t->toArray(), $expect->toArray());

        $category->delete( 'test' );
        $this->assertFalse($category->has( 'test'));

        $this->assertFalse(file_exists(Demo::YAML . '/test.yml' ));
        $this->assertFalse(file_exists(Demo::JSON . '/test.yml' ));

        $e = null;
        try {
            $t = $category->find('test');
        } catch (OptionNotFoundException $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertTrue($e instanceof OptionNotFoundException);

        $t1 = TestOption::createById('test1');
        $t2 = TestOption::createById('test2');


        $category->save($t1);
        $category->save($t2);

        $this->assertEquals(2, $category->count());

        $t3 = new TestOption(['id' => 'test1', 'a'=> 'B']);
        $category->save($t3, true);

        /**
         * @var TestOption $t
         */
        $t = $category->find('test1');
        $this->assertEquals('A', $t->a);

        // 变更 test1 的值
        $category->save($t3, false);
        $t = $category->find('test1');
        $this->assertEquals('B', $t->a);

        // 检查拥有的值.
        $ids = $category->searchIds('*');
        sort($ids);
        $this->assertEquals(['test1', 'test2'], $ids);

        $category->delete(...$ids);
        $ids = $category->searchIds('*');

        $this->assertEmpty($ids);

        $category->save($t1);
        $category->save($t2);

        $this->assertEquals(2, $category->count());
    }

}