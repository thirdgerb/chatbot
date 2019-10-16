<?php


namespace Commune\Test\Support\OptionRepo;


use Commune\Support\OptionRepo\Exceptions\OptionNotFoundException;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Container\Container;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Support\OptionRepo\Demo\Demo;
use Commune\Support\OptionRepo\Demo\TestOption;
use Commune\Support\OptionRepo\Impl\OptionRepositoryImpl;
use Commune\Support\OptionRepo\Storage\Arr\PHPRootStorage;
use Commune\Support\OptionRepo\Storage\Arr\PHPStorageMeta;
use Commune\Support\OptionRepo\Storage\Json\JsonRootStorage;
use Commune\Support\OptionRepo\Storage\Json\JsonStorageMeta;
use Commune\Support\OptionRepo\Storage\Yaml\YamlRootStorage;
use Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta;
use Commune\Support\OptionRepo\Storage\Memory\MemoryStorage;
use Commune\Support\OptionRepo\Storage\Memory\MemoryStorageMeta;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class OptionRepoTest extends TestCase
{
    /**
     * @param CategoryMeta[] $metas
     * @return Container
     * @throws
     */
    protected function prepareContainer(array $metas) : Container
    {
        $container = new Container();
        $container->instance(ContainerInterface::class, $container);
        $container->instance(LoggerInterface::class, new Logger('test'));

        $container->singleton(OptionRepository::class, OptionRepositoryImpl::class);

        $container->singleton(JsonRootStorage::class);
        $container->singleton(PHPRootStorage::class);
        $container->singleton(YamlRootStorage::class);
        $container->singleton(MemoryStorage::class);

        if (!empty($metas)) {
            foreach ($metas as $meta) {
                /**
                 * @var OptionRepository $repo
                 */
                $repo = $container->get(OptionRepository::class);
                $repo->registerCategory($meta);
            }
        }

        return $container;
    }

    protected function prepareMetas(string $metaId, string $optionName, array $orders) : CategoryMeta
    {
        $data = [
            'name' => $metaId,
            'optionClazz' => $optionName,
            'rootStorage' => null,
            'storagePipeline' => [],
            'constants' =>  [],
        ];

        $storages = [
            'mem' => [
                'meta' => MemoryStorageMeta::class,
                'config' => [
                    'name' => 'memory',
                    'expire' => 2,
                ]
            ],

            'json' => [
                'meta' => JsonStorageMeta::class,
                'config' => [
                    'name' => 'json',
                    'path' => Demo::JSON,
                ]
            ],
            'yaml' => [
                'meta' => YamlStorageMeta::class,
                'config' => [
                    'name' => 'yaml',
                    'path' => Demo::YAML,
                ]
            ],
            'php' => [
                'meta' => PHPStorageMeta::class,
                'config' => [
                    'name' => 'php',
                    'path' => Demo::PHP . '/rootPhp.php',
                    'isDir' => false,
                ]
            ],
        ];

        $root = array_pop($orders);

        $data['rootStorage'] = $storages[$root];

        while($name = array_shift($orders)) {
            $data['storagePipeline'][] = $storages[$name];
        }

        return new CategoryMeta($data);
    }

    public function testPHPFileRepo()
    {
        $meta = $this->prepareMetas('test', TestOption::class, ['mem', 'json', 'yaml', 'php' ]);

        $c = $this->prepareContainer([$meta]);

        /**
         * @var OptionRepository $repo
         */
        $repo = $c->get(OptionRepository::class);

        $repo->save('test', $expect = TestOption::createById('test'));

        $t = $repo->find('test', 'test' );

        $this->assertTrue(isset($t));
        $this->assertEquals($t->toArray(), $expect->toArray());

        $each = $repo->findAllVersions('test', 'test');
        $this->assertNotEmpty($each);

        foreach ($each as $option) {
            $this->assertEquals($expect->getHash(), $option->getHash());
        }

        $repo->delete( 'test', 'test');
        $this->assertFalse($repo->has( 'test', 'test'));

        $this->assertFalse(file_exists(Demo::YAML . '/test.yaml' ));
        $this->assertFalse(file_exists(Demo::JSON . '/test.yaml' ));

        $e = null;
        try {

            $repo->find('test', 'test');
        } catch (OptionNotFoundException $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertTrue($e instanceof OptionNotFoundException);
    }

    public function testJsonDirectoryRepo()
    {
        $meta = $this->prepareMetas('json', TestOption::class, ['mem',  'yaml' , 'json']);

        $c = $this->prepareContainer([$meta]);

        /**
         * @var OptionRepository $repo
         */
        $repo = $c->get(OptionRepository::class);
        $this->assertTrue($repo->has('json', 'notRootJson'));
        $a = $repo->find('json', 'notRootJson');
        $this->assertTrue($a instanceof TestOption);


        $data = $origin = $a->toArray();
        $originA = $data['a'];
        $data['a'] = 'F';
        $repo->save( 'json', new TestOption($data));

        $this->assertEquals('F', $repo->find('json', 'notRootJson')->a);

        $data['a'] = $originA;
        $repo->save( 'json', new TestOption($data));

        $this->assertEquals($origin, $repo->find('json', 'notRootJson')->toArray());
    }
}