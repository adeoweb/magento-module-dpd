<?php

namespace AdeoWeb\Dpd\Model\ResourceModel;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Setup\Schema;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class PickupPoint
 * @codeCoverageIgnore
 */
class PickupPoint extends AbstractDb
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(
            Schema::TABLE_DPD_PICKUP_POINT,
            PickupPointInterface::PICKUP_POINT_ID
        );
    }
}