<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Setup;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Store\Model\Store;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    public function __construct(
        ConfigInterface $resourceConfig
    ) {
        $this->resourceConfig = $resourceConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->deleteConfig('carriers/dpd/classic/restrictions');
            $this->deleteConfig('carriers/dpd/pickup/restrictions');
            $this->deleteConfig('carriers/dpd/saturday/restrictions');
            $this->deleteConfig('carriers/dpd/sameday/restrictions');
        }
    }

    private function deleteConfig(string $path)
    {
        $this->resourceConfig->deleteConfig($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, Store::DEFAULT_STORE_ID);
    }
}
