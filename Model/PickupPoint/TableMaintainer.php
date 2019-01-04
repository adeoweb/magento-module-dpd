<?php

namespace AdeoWeb\Dpd\Model\PickupPoint;

use AdeoWeb\Dpd\Setup\Schema;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class TableMaintainer
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var AdapterInterface
     */
    private $connection;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @return AdapterInterface
     */
    public function getConnection()
    {
        if (null === $this->connection) {
            $this->connection = $this->resource->getConnection();
        }
        return $this->connection;
    }

    /**
     * @return void
     */
    public function resetTable()
    {
        if ($this->getConnection()->isTableExists(Schema::TABLE_DPD_PICKUP_POINT)) {
            $this->getConnection()->truncateTable(Schema::TABLE_DPD_PICKUP_POINT);
        }
    }
}