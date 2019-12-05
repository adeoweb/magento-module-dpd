<?php

namespace AdeoWeb\Dpd\Api\Data\Shipping;

interface DeliveryOptionsInterface
{
    const INDEX_API_ID = 'api_id';
    const INDEX_DELIVERY_TIME = 'delivery_time';

    /**
     * @return string
     */
    public function getApiId();

    /**
     * @param string $id
     * @return void
     */
    public function setApiId($id);

    /**
     * @return int
     */
    public function getDeliveryTime();

    /**
     * @param $id
     * @return void
     */
    public function setDeliveryTime($id);
}
