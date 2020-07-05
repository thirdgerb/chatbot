<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Impl;

use Psr\Container\ContainerInterface;
use Commune\Support\Registry\Storage;
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\StorageDriver;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStorage implements Storage
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var StorageOption
     */
    protected $storageOption;

    /**
     * @var StorageDriver
     */
    protected $driver;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * IStorage constructor.
     * @param ContainerInterface $container
     * @param StorageOption $storageOption
     */
    public function __construct(ContainerInterface $container, StorageOption $storageOption)
    {
        $this->container = $container;
        $this->storageOption = $storageOption;
    }

    public function getDriver() : StorageDriver
    {
        return $this->driver
            ?? $this->driver = $this->container->get(
                $this->storageOption->getDriver()
            );
    }

    public function getOption(): StorageOption
    {
        return $this->storageOption;
    }


}