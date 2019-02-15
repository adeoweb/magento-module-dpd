<?php

namespace AdeoWeb\Dpd\Cron;

use AdeoWeb\Dpd\Api\PickupPointManagementInterface;
use Psr\Log\LoggerInterface;

class UpdatePickupPointList
{
    /**
     * @var PickupPointManagementInterface
     */
    private $pickupPointManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        PickupPointManagementInterface $pickupPointManagement,
        LoggerInterface $logger
    ) {
        $this->pickupPointManagement = $pickupPointManagement;
        $this->logger = $logger;
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $result = $this->pickupPointManagement->update();

        if (\is_array($result)) {
            foreach ($result as $languageCode => $warning) {
                $this->logger->warning(\sprintf(
                    '<error>Error encountered while updating DPD pickup point list for "%s": %s</error>',
                    $languageCode,
                    $warning
                ));
            }
        }
    }
}