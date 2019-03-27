<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\CloseManifestManagementInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelManifestPrintRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use Magento\Framework\Exception\LocalizedException;

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
            (clone $today)->modify('+1 day'),
            (clone $today)->modify('+2 days'),
            (clone $today)->modify('+3 days')
        ];

        $pdfList = [];

        foreach ($dates as $date) {
            $parcelManifestPrintRequest = $this->parcelManifestPrintRequestFactory->create();
            $parcelManifestPrintRequest->setDate($date->format('Y-m-d'));
            $parcelManifestPrintRequest->setType('manifest');
            $parcelManifestPrintRequest->setFormat('pdf');

            $response = $this->carrierService->call($parcelManifestPrintRequest);

            if (!empty($response) && \json_decode($response) === null) {
                $pdfList[] = $response;
            }
        }

        if (empty($pdfList)) {
            throw new LocalizedException(__('No new shipments were created.'));
        }

        return $pdfList;
    }
}