<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use AdeoWeb\Dpd\Config\General;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelPrintRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use Magento\Framework\Exception\LocalizedException;

class PrintLabelManagement implements PrintLabelManagementInterface
{
    /**
     * @var ParcelPrintRequestFactory
     */
    private $parcelPrintRequestFactory;

    /**
     * @var General
     */
    private $moduleConfig;

    /**
     * @var ServiceInterface
     */
    private $dpdService;

    public function __construct(
        ParcelPrintRequestFactory $parcelPrintRequestFactory,
        General $moduleConfig,
        ServiceInterface $dpdService
    ) {
        $this->parcelPrintRequestFactory = $parcelPrintRequestFactory;
        $this->moduleConfig = $moduleConfig;
        $this->dpdService = $dpdService;
    }

    /**
     * @param array $labelNumbers
     * @return string|null
     * @throws \Exception
     */
    public function printLabels($labelNumbers)
    {
        if (empty($labelNumbers)) {
            return null;
        }

        $parcelPrintRequest = $this->parcelPrintRequestFactory->create();
        $parcelPrintRequest->setParcels($labelNumbers);
        $parcelPrintRequest->setPrintFormat($this->moduleConfig->getPrintLabelFormat());

        $response = $this->dpdService->call($parcelPrintRequest);

        if (strpos($response, '%PDF-') !== 0 && Serializer::isJson($response)) {
            $result = \json_decode($response, true);

            if (isset($result['errlog'])) {
                throw new \Exception('API Error: ' . $result['errlog']);
            }
        }

        return $response;
    }
}
