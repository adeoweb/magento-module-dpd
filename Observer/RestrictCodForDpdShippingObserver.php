<?php

namespace AdeoWeb\Dpd\Observer;

use AdeoWeb\Dpd\Api\Data\Shipping\DeliveryOptionsInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Quote\Model\Quote;

class RestrictCodForDpdShippingObserver implements ObserverInterface
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var PickupPointRepositoryInterface
     */
    private $pickupPointRepository;

    /**
     * @var array
     */
    private $allowedPickupPointIdentifiers = ['EE90', 'EE10', 'LV90', 'LV10', 'LT90'];

    /**
     * @param Serializer $serializer
     * @param PickupPointRepositoryInterface $pickupPointRepository
     */
    public function __construct(
        Serializer $serializer,
        PickupPointRepositoryInterface $pickupPointRepository
    ) {
        $this->serializer = $serializer;
        $this->pickupPointRepository = $pickupPointRepository;
    }

    /**
     * Public Method
     *
     * @param Observer $observer
     * @return DataObject
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        /** @var DataObject $checkResult */
        $checkResult = $observer->getEvent()->getData('result');

        /** @var AbstractMethod $paymentMethodInstance */
        $paymentMethodInstance = $observer->getEvent()->getData('method_instance');

        if (!$quote || $paymentMethodInstance->getCode() !== 'cashondelivery') {
            return null;
        }

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();

        if ($shippingMethod !== 'dpd_pickup') {
            return null;
        }

        $deliveryOptions = $this->serializer->unserialize($quote->getData('dpd_delivery_options'));

        if (!$deliveryOptions || !isset($deliveryOptions[DeliveryOptionsInterface::INDEX_API_ID])) {
            return null;
        }

        $apiId = $deliveryOptions[DeliveryOptionsInterface::INDEX_API_ID];

        try {
            $pickupPoint = $this->pickupPointRepository->getByApiId($apiId);
        } catch (NoSuchEntityException $e) {
            return $checkResult->setData('is_available', false);
        }

        $pickupPointIdentifier = \substr($pickupPoint->getApiId(), 0, 4);

        if (!\in_array($pickupPointIdentifier, $this->allowedPickupPointIdentifiers)) {
            return $checkResult->setData('is_available', false);
        }

        return $checkResult->setData('is_available', true);
    }
}
