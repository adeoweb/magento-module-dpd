<?php

namespace AdeoWeb\Dpd\Model\ResourceModel\PickupPoint;

use AdeoWeb\Dpd\Model\PickupPoint;
use AdeoWeb\Dpd\Model\ResourceModel\PickupPoint as PickupPointResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @codeCoverageIgnore
 */
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

    /**
     * @return Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this as $item) {
            $this->_resource->unserializeFields($item);
        }

        return $this;
    }
}
