<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Setup;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

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
            $this->resourceConfig->deleteConfig('carriers/dpd/classic/restrictions');
            $this->resourceConfig->deleteConfig('carriers/dpd/pickup/restrictions');
            $this->resourceConfig->deleteConfig('carriers/dpd/saturday/restrictions');
            $this->resourceConfig->deleteConfig('carriers/dpd/sameday/restrictions');
        }
    }
}
