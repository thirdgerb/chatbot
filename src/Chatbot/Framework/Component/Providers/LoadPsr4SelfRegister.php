<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\App\Components\Configurable\Controllers\EntryIntent;
use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\SelfRegister;
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
     * @param $app
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

    public function boot($app)
    {

        $finder = new Finder();
        $finder->files()
            ->in($this->path)
            ->name('/\.php$/');

        foreach ($finder as $fileInfo) {

            $path = $fileInfo->getPathname();
            $name = str_replace($this->path, '', $path);
            $name = str_replace('.php', '', $name);
            $name = str_replace('/', '\\', $name);

            $clazz = trim($this->domain, '\\')
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

            $this->logger->debug("register context $clazz");
            $method = [$clazz, SelfRegister::REGISTER_METHOD];
            call_user_func($method);
        }
    }

    public function register()
    {
    }


}