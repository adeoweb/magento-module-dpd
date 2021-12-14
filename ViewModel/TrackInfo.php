<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Shipping\Model\Order\TrackFactory;

use function is_array;

class TrackInfo implements ArgumentInterface
{
    /**
     * @var array
     */
    private $trackingInfo = [];

    /**
     * @var TrackFactory
     */
    private $trackFactory;

    public function __construct(
        TrackFactory $trackFactory
    ) {
        $this->trackFactory = $trackFactory;
    }

    public function getTrackingInfoByTrackId(int $trackId): array
    {
        $track = $this->trackFactory->create()->load($trackId);

        if ($track->getId()) {
            $numberDetail = $track->getNumberDetail();
            $this->trackingInfo = is_array($numberDetail) ? $numberDetail : $numberDetail->toArray();
        }

        return $this->trackingInfo;
    }
}
