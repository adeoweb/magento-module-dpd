<?php

namespace AdeoWeb\Dpd\Api\Data\Shipping;

interface DeliveryOptionsInterface
{
    public const INDEX_API_ID = 'api_id';
    public const INDEX_DELIVERY_TIME = 'delivery_time';

    /**
     * Public method
     *
     * @return string
     */
    public function getApiId();

    /**
     * Public method
     *
     * @param string $id
     * @return void
     */
    public function setApiId($id);

    /**
     * Public method
     *
     * @return int
     */
    public function getDeliveryTime();

    /**
     * Public method
     *
     * @param int $id
     * @return void
     */
    public function setDeliveryTime($id);
}
