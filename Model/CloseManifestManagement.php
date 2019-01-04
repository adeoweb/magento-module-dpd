<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\CloseManifestManagementInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelManifestPrintRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;

class CloseManifestManagement implements CloseManifestManagementInterface
{
    /**
     * @var ParcelManifestPrintRequestFactory
     */
    private $parcelManifestPrintRequestFactory;

    /**
     * @var ServiceInterface
     */
    private $carrierService;

    public function __construct(
        ParcelManifestPrintRequestFactory $parcelManifestPrintRequestFactory,
        ServiceInterface $carrierService
    ) {
        $this->parcelManifestPrintRequestFactory = $parcelManifestPrintRequestFactory;
        $this->carrierService = $carrierService;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function closeManifest()
    {
        $today = new \DateTime;
        $dates = [
            $today,
            $today->modify('+1 day'),
            $today->modify('+2 days'),
            $today->modify('+3 days')
        ];

        $pdfList = [];

        foreach ($dates as $date) {
            $parcelManifestPrintRequest = $this->parcelManifestPrintRequestFactory->create();
            $parcelManifestPrintRequest->setDate($date->format('Y-m-d'));

            $response = $this->carrierService->call($parcelManifestPrintRequest);

            if ($response->hasError()) {
                throw new \Exception($response->getErrorMessage());
            }

            $pdfList[] = $response->getBody('pdf');
        }

        return $pdfList;
    }
}