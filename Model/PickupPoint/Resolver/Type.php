<?php

namespace AdeoWeb\Dpd\Model\PickupPoint\Resolver;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Model\PickupPoint\ResolverInterface;

class Type implements ResolverInterface
{
    /**
     * @param PickupPointInterface $pickupPoint
     * @return int
     */
    public function resolve(PickupPointInterface $pickupPoint)
    {
        $pickupPointApiId = $pickupPoint->getApiId();

        if (!$pickupPointApiId) {
            throw new \RuntimeException('Invalid pickup point provided. Parcelshop ID is missing');
        }

        $typeIdentifier = \substr($pickupPointApiId, 0, 4);

        switch ($typeIdentifier) {
            case 'EE90':
            case 'LV90':
            case 'LT90':
                return PickupPointInterface::TYPE_LOCKER;
            case 'EE10':
            case 'LV10':
            case 'LT10':
                return PickupPointInterface::TYPE_PARCELSHOP;
            case 'EE91':
                return PickupPointInterface::TYPE_ROBOT;
        }

        return null;
    }
}