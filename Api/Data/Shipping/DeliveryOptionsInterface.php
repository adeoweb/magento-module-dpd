<?php

namespace AdeoWeb\Dpd\Api\Data\Shipping;

interface DeliveryOptionsInterface
{
    const INDEX_PICKUP_POINT_ID = 'pickup_point_id';
    const INDEX_DELIVERY_TIME = 'delivery_time';

    /**
     * @return int
     */
    public function getPickupPointId();

    /**
     * @param $id
     * @return void
     */
    public function setPickupPointId($id);

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