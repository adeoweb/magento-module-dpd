<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Model\Service\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class ParcelPrintRequest extends AbstractRequest implements RequestInterface
{
    const ENDPOINT = 'parcelPrint_';

    protected $params = [
        'parcels',
        'printType',
        'printFormat'
    ];

    protected $requiredParams = [
        'parcels'
    ];

    /**
     * @param string|array $parcels
     * @return ParcelPrintRequest
     */
    public function setParcels($parcels)
    {
        if (\is_array($parcels)) {
            $parcels = \implode('|', $parcels);
        }

        return $this->setData('parcels', $parcels, 3000);
    }

    /**
     * @param string $printType
     * @return ParcelPrintRequest
     */
    public function setPrintType($printType)
    {
        return $this->setData('printType', $printType, 20);
    }

    /**
     * @param string $printFormat
     * @return ParcelPrintRequest
     */
    public function setPrintFormat($printFormat)
    {
        return $this->setData('printFormat', $printFormat, 2);
    }

    /**
     * {@inheritDoc}
     */
    public function isFile()
    {
        return true;
    }
}
