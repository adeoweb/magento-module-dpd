<?php

namespace AdeoWeb\Dpd\Model\Shipping;

use AdeoWeb\Dpd\Api\Data\Shipping\DeliveryOptionsInterface;
use Magento\Framework\DataObject;

class DeliveryOptions extends DataObject implements DeliveryOptionsInterface
{
    /**
     * @return int
     */
    public function getPickupPointId()
    {
        return $this->getData(self::INDEX_PICKUP_POINT_ID);
    }

    /**
     * @param $id
     * @return void
     */
    public function setPickupPointId($id)
    {
        $this->setData(self::INDEX_PICKUP_POINT_ID, (int)$id);
    }

    /**
     * @return int
     */
    public function getDeliveryTime()
    {
        return $this->getData(self::INDEX_DELIVERY_TIME);
    }

    /**
     * @param $id
     * @return void
     */
    public function setDeliveryTime($id)
    {
        $this->setData(self::INDEX_DELIVERY_TIME, (int)$id);
    }
}