<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Model\Service\RequestInterface;

/**
 * Class ParcelDeleteRequest
 * @codeCoverageIgnore
 */
class ParcelDeleteRequest extends AbstractRequest implements RequestInterface
{
    const ENDPOINT = 'parcelDelete_';

    protected $params = [
        'parcels'
    ];

    protected $requiredParams = [
        'parcels'
    ];

    /**
     * @param string|array $parcels
     * @return ParcelDeleteRequest
     */
    public function setParcels($parcels)
    {
        if (\is_array($parcels)) {
            $parcels = \implode('|', $parcels);
        }

        return $this->setData('parcels', $parcels, 3000);
    }
}