<?php

namespace AdeoWeb\Dpd\Model\Shipping;

use AdeoWeb\Dpd\Api\Data\Shipping\DeliveryOptionsInterface;
use Magento\Framework\DataObject;

/**
 * @codeCoverageIgnore
 */
class DeliveryOptions extends DataObject implements DeliveryOptionsInterface
{
    /**
     * @return string
     */
    public function getApiId()
    {
        return $this->getData(self::INDEX_API_ID);
    }

    /**
     * @param string $id
     * @return void
     */
    public function setApiId($id)
    {
        $this->setData(self::INDEX_API_ID, (string)$id);
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
