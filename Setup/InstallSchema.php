<?php

namespace AdeoWeb\Dpd\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritDoc}
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->createPickupPointTable($installer);
        $this->createLocationTable($installer);

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createPickupPointTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(Schema::TABLE_DPD_PICKUP_POINT);
        $this->dropTableIfExists($installer, $tableName);

        $table = $installer->getConnection()->newTable($tableName);
        $this->addColumnsToPickupPointTable($table);

        $table->setComment('DPD Pickup Points');
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createLocationTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(Schema::TABLE_DPD_LOCATION);
        $this->dropTableIfExists($installer, $tableName);

        $table = $installer->getConnection()->newTable($tableName);
        $this->addColumnsToLocationTable($table);

        $table->setComment('DPD Locations');
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function addColumnsToPickupPointTable(Table $table)
    {
        return $table->addColumn(
            'pickup_point_id',
            Table::TYPE_INTEGER,
            10,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Pickup Point ID'
        )->addColumn(
            'api_id',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'API ID'
        )->addColumn(
            'type',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Type'
        )->addColumn(
            'company',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Company'
        )->addColumn(
            'country',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Country'
        )->addColumn(
            'city',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'City'
        )->addColumn(
            'postcode',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Postcode'
        )->addColumn(
            'street',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Street'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Email'
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Phone'
        )->addColumn(
            'longitude',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Longitude'
        )->addColumn(
            'latitude',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Latitude'
        );
    }

    /**
     * @param Table $table
     * @throws \Zend_Db_Exception
     */
    public function addColumnsToLocationTable(Table $table)
    {
        $table->addColumn(
            'location_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'identity' => true, 'auto_increment' => true, 'unsigned' => true, 'primary' => true],
            'Location Id'
        )->addColumn(
            'type',
            Table::TYPE_INTEGER,
            255,
            ['nullable' => false],
            'Location Type'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location Name'
        )->addColumn(
            'address',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location Address'
        )->addColumn(
            'city',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location City'
        )->addColumn(
            'country',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location Country'
        )->addColumn(
            'postcode',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location Postcode'
        )->addColumn(
            'additional_info',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location Additional Info'
        )->addColumn(
            'contact_name',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location Contact Name'
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location Phone'
        )->addColumn(
            'work_until',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Location Work Until'
        )->addColumn(
            'created_at',
            Table::TYPE_DATETIME,
            null,
            ['unsigned' => true, 'nullable' => true],
            'created_at'
        )->addColumn(
            'updated_at',
            Table::TYPE_DATETIME,
            null,
            ['unsigned' => true, 'nullable' => true],
            'updated_at'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param string $table
     */
    private function dropTableIfExists($installer, $table)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable($table))) {
            $installer->getConnection()->dropTable(
                $installer->getTable($table)
            );
        }
    }
}
