<?php

namespace AdeoWeb\Dpd\Model\PickupPoint;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;

interface ResolverInterface
{
    /**
     * @param PickupPointInterface $pickupPoint
     * @return mixed
     */
    public function resolve(PickupPointInterface $pickupPoint);
}
