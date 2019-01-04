<?php

namespace AdeoWeb\Dpd\Api;

interface DeliveryTimeManagementInterface
{
    /**
     * @param string $city
     * @return mixed
     * @throws \Exception
     */
    public function calculate($city);
}