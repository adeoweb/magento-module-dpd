<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Setup\Patch\Data;

use AdeoWeb\Dpd\Model\Provider\MetaData\ModuleMetaDataInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Store;

class DeleteConfigurations implements DataPatchInterface
{
    /** @var ModuleMetaDataInterface */
    private $moduleMetaData;

    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var ConfigInterface */
    private $resourceConfig;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ModuleMetaDataInterface $moduleMetaData,
        ConfigInterface $resourceConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->moduleMetaData = $moduleMetaData;
        $this->resourceConfig = $resourceConfig;
    }

    public function apply(): void
    {
        $installer = $this->moduleDataSetup;

        $installer->startSetup();

        if (version_compare($this->moduleMetaData->getVersion(), '1.0.1', '<')) {
            $this->deleteConfig('carriers/dpd/classic/restrictions');
            $this->deleteConfig('carriers/dpd/pickup/restrictions');
            $this->deleteConfig('carriers/dpd/saturday/restrictions');
            $this->deleteConfig('carriers/dpd/sameday/restrictions');
        }

        $installer->endSetup();
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }

    private function deleteConfig(string $path): void
    {
        $this->resourceConfig->deleteConfig(
            $path,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            Store::DEFAULT_STORE_ID
        );
    }
}
