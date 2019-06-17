<?php


namespace Commune\Chatbot\App\Components\Configurable\Drivers;


use Commune\Chatbot\App\Components\Configurable\Configs\DomainConfig;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class JsonDomainRepository implements DomainConfigRepository
{

    /**
     * @var string
     */
    protected $defaultPath;

    /**
     * @var DomainConfig[]
     */
    protected $domains = [];

    protected $paths = [];

    protected $updated = [];

    protected $removed = [];

    /**
     * JsonDomainRepository constructor.
     * @param string $defaultPath
     */
    public function __construct(string $defaultPath)
    {
        $this->defaultPath = $defaultPath;
        if (!realpath($defaultPath)) {
            throw new ConfigureException(
                static::class
                . " $defaultPath is not valid path"
            );
        }
    }


    public function preload(): void
    {
        $finder = new Finder();
        $finder = $finder->in($this->defaultPath)->name('/\.json$/');
        foreach ($finder as $file) {
            /**
             * @var SplFileInfo $file
             */
            $path = $file->getRealPath();
            $this->addResource($path);
        }
    }

    public function each(): \Generator
    {
        foreach ($this->domains as $domain) {
            yield $domain;
        }
    }

    /**
     * @param string $resource json file path
     */
    public function addResource($resource): void
    {
        if (!file_exists($resource)) {
            throw new ConfigureException(
                __METHOD__
                . " file $resource not exists"
            );
        }

        $json = file_get_contents($resource);
        $data = json_decode($json, true);
        if (empty($data)) {
            throw new ConfigureException(
                __METHOD__
                . ' get invalid data from ' . $resource
            );
        }

        $domain = new DomainConfig($data);
        $this->domains[$domain->domain] = $domain;
        $this->paths[$domain->domain] = $resource;
    }

    public function newDomain(string $name): void
    {
        $this->domains[$name] = new DomainConfig([
            'domain' => $name,
        ]);
        $this->updated[] = $name;
    }


    public function paginateDomainNames(int $limit = 0, int $offset = 0): array
    {
        return array_slice(array_map(function(DomainConfig $domain){
            return $domain->domain;
        }, $this->domains), $offset, $limit);
    }

    public function getDomainCount(): int
    {
        return count($this->domains);
    }

    public function update(DomainConfig $domain): void
    {
        $name = $domain->domain;
        $this->domains[$name] = $domain;
        $this->updated[] = $name;
    }

    protected function fetchPath(string $name) : string
    {
        if (!isset($this->paths[$name])) {
            $path = rtrim($this->defaultPath, '/') . '/' . $name . '.json';
            $this->paths[$name] = $path;
            return $path;
        }
        return $this->paths[$name];
    }


    public function get(string $domain): ? DomainConfig
    {
        return $this->domains[$domain] ?? null;
    }

    public function has(string $domain): bool
    {
        return isset($this->domains[$domain]);
    }

    public function save(): void
    {
        foreach ($this->updated as $domainName) {
            if (isset($this->domains[$domainName])) {
                $data = $this->domains[$domainName]->toPrettyJson();
                $path = $this->fetchPath($domainName);
                @file_put_contents($path, $data);
            }
        }
        $this->updated = [];

        foreach ($this->removed as $name => $config) {
            $path = $this->fetchPath($name);
            @unlink($path);
        }
    }

    public function getCount(): int
    {
        return count($this->domains);
    }

    public function remove(string $domain) : int
    {
        if (isset($this->domains[$domain])) {
            $config = $this->domains[$domain];
            unset($this->domains[$domain]);
            $this->removed[$config->domain] = $domain;
            return 1;
        }
        return 0;
    }



}