<?php

namespace AdeoWeb\Dpd\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritDoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->removeWorkUntilField($installer);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addOpeningHoursField($installer);
        }

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function removeWorkUntilField(SchemaSetupInterface $installer)
    {
        $table = $installer->getTable(Schema::TABLE_DPD_LOCATION);

        $installer->getConnection()->dropColumn($table, 'work_until');
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function addOpeningHoursField(SchemaSetupInterface $installer)
    {
        $table = $installer->getTable(Schema::TABLE_DPD_PICKUP_POINT);

        $installer->getConnection()->addColumn(
            $table,
            'opening_hours',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Opening Hours'
            ]
        );
    }
}
