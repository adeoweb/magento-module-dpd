<?php

namespace AdeoWeb\Dpd\Api;

interface PickupPointManagementInterface
{
    /**
     * @param string $country
     * @param string $city
     * @return mixed
     */
    public function getList($country = null, $city = null);

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update();
}