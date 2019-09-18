<?php

namespace AdeoWeb\Dpd\Plugin\Model;

use AdeoWeb\Dpd\Model\Carrier\MethodFactoryPool;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

class ShippingInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MethodFactoryPool
     */
    private $methodFactoryPool;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger,
        MethodFactoryPool $methodFactoryPool
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->methodFactoryPool = $methodFactoryPool;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        if ($addressInformation->getShippingCarrierCode() !== 'dpd') {
            return [$cartId, $addressInformation];
        }

        try {
            $quote = $this->quoteRepository->getActive($cartId);
        } catch (NoSuchEntityException $e) {
            return [$cartId, $addressInformation];
        }

        $deliveryOptions = $this->readDeliveryOptionsFromAddressInformation($addressInformation);

        if (!$deliveryOptions) {
            return [$cartId, $addressInformation];
        }

        $method = $this->methodFactoryPool->getInstance($addressInformation->getShippingMethodCode());

        if (!$method) {
            return [$cartId, $addressInformation];
        }

        $method->validateDeliveryOptions($deliveryOptions);

        $serializedDeliveryOptions = $deliveryOptions->toJson();

        if ($serializedDeliveryOptions === $quote->getData('dpd_delivery_options')) {
            return [$cartId, $addressInformation];
        }

        $quote->setData('dpd_delivery_options', $serializedDeliveryOptions);

        try {
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return [$cartId, $addressInformation];
    }

    /**
     * @param ShippingInformationInterface $addressInformation
     * @return \AdeoWeb\Dpd\Model\Shipping\DeliveryOptions|null
     */
    private function readDeliveryOptionsFromAddressInformation(ShippingInformationInterface $addressInformation)
    {
        return $addressInformation->getShippingAddress()->getExtensionAttributes()->getDpdDeliveryOptions();
    }
}
