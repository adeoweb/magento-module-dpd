<?php

declare(strict_types = 1);

namespace AdeoWeb\Dpd\Model\Provider\MetaData;

use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Component\ComponentRegistrar;

class ModuleMetaData implements ModuleMetaDataInterface
{
    private const MODULE_NAME = 'AdeoWeb_Dpd';
    private const COMPOSER_JSON_FILE = 'composer.json';

    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var Json
     */
    private $json;

    public function __construct(
        ComponentRegistrarInterface $componentRegistrar,
        ReadFactory $readFactory,
        Json $json
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
        $this->json = $json;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        $path = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            self::MODULE_NAME
        );

        $directoryRead = $this->readFactory->create($path);

        $jsonData = $this->json->unserialize($directoryRead->readFile(self::COMPOSER_JSON_FILE));

        return !empty($jsonData['version']) ? $jsonData['version'] : 'Read error!';
    }
}
