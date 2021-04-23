<?php

namespace AdeoWeb\Dpd\Model\Provider\PickupPoint;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;

interface AllowedWeightsInterface
{
    /**
     * @param CartInterface $quote
     * @param AddressInterface $address
     * @return bool
     */
    public function validate(CartInterface $quote, AddressInterface $address): bool;
}
