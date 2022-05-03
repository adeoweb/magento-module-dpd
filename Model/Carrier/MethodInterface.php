<?php

namespace AdeoWeb\Dpd\Model\Carrier;

use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

interface MethodInterface
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return Method
     */
    public function getRateResult(): Method;

    /**
     * @param RateRequest $request
     */
    public function setRequest(RateRequest $request): void;

    /**
     * @return bool
     */
    public function validate(): bool;

    /**
     * @param DataObject $deliveryOptions
     * @return bool
     */
    public function validateDeliveryOptions(DataObject $deliveryOptions): bool;

    /**
     * @param CreateShipmentRequest $createShipmentRequest
     * @param DataObject $request
     * @return CreateShipmentRequest
     */
    public function processShipmentRequest(
        CreateShipmentRequest $createShipmentRequest,
        DataObject $request
    ): CreateShipmentRequest;
}
