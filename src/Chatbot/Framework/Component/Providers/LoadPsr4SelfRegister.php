<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Container\ContainerContract;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;

class LoadPsr4SelfRegister extends ServiceProvider
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LoadPsr4SelfRegister constructor.
     * @param ContainerContract $app
     * @param LoggerInterface $logger
     * @param string $psr4ns
     * @param string $path
     */
    public function __construct(
        $app,
        LoggerInterface $logger,
        string $psr4ns,
        string $path
    )
    {
        $this->logger = $logger;
        $this->domain = $psr4ns;
        $this->path = $path;
        if (!realpath($this->path)) {
            throw new ConfigureException(
                "path $path not exists"
            );
        }
        parent::__construct($app);
    }

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
        static::loadSelfRegister(
            $app,
            $this->domain,
            $this->path,
            $this->logger
        );

    }

    public static function loadSelfRegister(
        ContainerContract $processContainer,
        string $namespace,
        string $directory,
        LoggerInterface $logger
    ) : void
    {
        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->name('/\.php$/');

        $i = 0;
        foreach ($finder as $fileInfo) {

            $path = $fileInfo->getPathname();
            $name = str_replace($directory, '', $path);
            $name = str_replace('.php', '', $name);
            $name = str_replace('/', '\\', $name);

            $clazz = trim($namespace, '\\')
                . '\\' .
                trim($name, '\\');

            if (!is_a($clazz, SelfRegister::class, TRUE)) {
                continue;
            }

            // 判断是不是可以实例化的.
            $r = new \ReflectionClass($clazz);
            if (!$r->isInstantiable()) {
                continue;
            }

            $logger->debug("register context $clazz");
            $method = [$clazz, SelfRegister::REGISTER_METHOD];
            call_user_func($method, $processContainer);
            $i ++;
        }

        if (empty($i)) {
            $logger->warning(
                'no self register class found,'
                . "namespace is $namespace,"
                . "directory is $directory"
            );
        }

    }


    public function register()
    {
    }


}