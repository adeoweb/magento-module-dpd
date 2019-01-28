<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Model\Service\RequestInterface;

/**
 * Class ParcelStatusRequest
 * @codeCoverageIgnore
 */
class ParcelStatusRequest extends AbstractRequest implements RequestInterface
{
    const ENDPOINT = 'parcelStatus_';

    protected $params = [
        'parcel_number'
    ];

    protected $requiredParams = [
        'parcel_number'
    ];

    /**
     * @param string $parcelNumber
     * @return ParcelStatusRequest
     */
    public function setParcelNumber($parcelNumber)
    {
        return $this->setData('parcel_number', $parcelNumber);
    }
}