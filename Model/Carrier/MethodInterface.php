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
    public function getCode();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return Method
     */
    public function getRateResult();

    /**
     * @param RateRequest $request
     */
    public function setRequest(RateRequest $request);

    /**
     * @return bool
     */
    public function validate();

    /**
     * @param CreateShipmentRequest $createShipmentRequest
     * @param DataObject $request
     * @return CreateShipmentRequest
     */
    public function processShipmentRequest(CreateShipmentRequest $createShipmentRequest, DataObject $request);
}