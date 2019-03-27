<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Model\Service\RequestInterface;

/**
 * Class ParcelManifestPrintRequest
 * @codeCoverageIgnore
 */
class ParcelManifestPrintRequest extends AbstractRequest implements RequestInterface
{
    const ENDPOINT = 'parcelManifestPrint_';

    protected $params = [
        'type',
        'date',
        'format'
    ];

    protected $requiredParams = [
        'date'
    ];

    /**
     * @param string $type
     * @return ParcelManifestPrintRequest
     */
    public function setType($type)
    {
        return $this->setData('type', $type, 20);
    }

    /**
     * @param string $date
     * @return ParcelManifestPrintRequest
     */
    public function setDate($date)
    {
        return $this->setData('date', $date, 10);
    }

    /**
     * @param string $format
     * @return ParcelManifestPrintRequest
     */
    public function setFormat($format)
    {
        return $this->setData('format', $format);
    }

    /**
     * {@inheritDoc}
     */
    public function isFile()
    {
        return true;
    }
}