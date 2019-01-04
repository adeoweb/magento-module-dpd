<?php

namespace AdeoWeb\Dpd\Model\ResourceModel\PickupPoint;

use AdeoWeb\Dpd\Model\PickupPoint;
use AdeoWeb\Dpd\Model\ResourceModel\PickupPoint as PickupPointResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * {@inheritDoc}
     */
    public function _construct()
    {
        $this->_init(
            PickupPoint::class,
            PickupPointResource::class
        );
    }
}