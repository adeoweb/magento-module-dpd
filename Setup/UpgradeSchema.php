<?php

namespace AdeoWeb\Dpd\Setup;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
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

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addDisabledField($installer);
            $this->addUniqueKey($installer);
        }

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function removeWorkUntilField(SchemaSetupInterface $installer)
    {
        $table = $installer->getTable(SchemaInterface::TABLE_DPD_LOCATION);

        $installer->getConnection()->dropColumn($table, 'work_until');
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function addOpeningHoursField(SchemaSetupInterface $installer)
    {
        $table = $installer->getTable(SchemaInterface::TABLE_DPD_PICKUP_POINT);

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

    private function addDisabledField(SchemaSetupInterface $installer)
    {
        $table = $installer->getTable(SchemaInterface::TABLE_DPD_PICKUP_POINT);

        $installer->getConnection()->addColumn(
            $table,
            PickupPointInterface::IS_DISABLED,
            [
                'type' => Table::TYPE_SMALLINT,
                'length' => 5,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is Disabled'
            ]
        );
    }

    private function addUniqueKey(SchemaSetupInterface $installer)
    {
        $table = $installer->getTable(SchemaInterface::TABLE_DPD_PICKUP_POINT);
        $connection = $installer->getConnection();

        $connection->addIndex(
            $table,
            $connection->getIndexName($table, [PickupPointInterface::API_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
            [PickupPointInterface::API_ID],
            AdapterInterface::INDEX_TYPE_UNIQUE
        );
    }
}
