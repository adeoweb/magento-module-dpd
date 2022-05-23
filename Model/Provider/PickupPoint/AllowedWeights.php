<?php

declare(strict_types = 1);

namespace AdeoWeb\Dpd\Model\Provider\PickupPoint;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\AddressInterface;

class AllowedWeights implements AllowedWeightsInterface
{
    private const ALLOWED_WEIGHTS = [
        'LT' => 31.5,
        'LV' => 31.5,
        'EE' => 31.5,
        'PT' => 10,
        'OTHER' => 20
    ];

    /**
     * @inheritDoc
     */
    public function validate(CartInterface $quote, AddressInterface $address): bool
    {
        $cartItems = $quote->getItems();

        $weight = 0;

        foreach ($cartItems as $item) {
            $weight += ($item->getWeight() * $item->getQty());
        }

        $code = $address->getCountryId();

        $allowedWeight = self::ALLOWED_WEIGHTS[$code] ?? self::ALLOWED_WEIGHTS['OTHER'];

        return $weight <= $allowedWeight;
    }
}
