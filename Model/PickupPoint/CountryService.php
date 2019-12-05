<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model\PickupPoint;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequestFactory;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequest;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

class CountryService
{
    /**
     * @var PickupPointSearchRequestFactory
     */
    private $pickupPointSearchRequestFactory;

    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * @var PickupPointRepositoryInterface
     */
    private $pickupPointRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        PickupPointSearchRequestFactory $pickupPointSearchRequestFactory,
        PickupPointRepositoryInterface $pickupPointRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ServiceInterface $service
    ) {
        $this->pickupPointSearchRequestFactory = $pickupPointSearchRequestFactory;
        $this->service = $service;
        $this->pickupPointRepository = $pickupPointRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param string $countryId
     * @return array
     * @throws LocalizedException
     */
    public function getPickupPoints($countryId)
    {
        /** @var PickupPointSearchRequest $request */
        $request = $this->pickupPointSearchRequestFactory->create();
        $request->setCountry($countryId);
        $request->setFetchAllByCountryFlag(true);
        $request->setRetrieveOpeningHoursFlag(true);

        $pickupPointListResponse = $this->service->call($request);

        if ($pickupPointListResponse->hasError()) {
            throw new LocalizedException(__($pickupPointListResponse->getErrorMessage()));
        }

        $response = $pickupPointListResponse->getBody('parcelshops');
        if (!is_array($response)) {
            throw new LocalizedException(__('Invalid parcel shops data.'));
        }

        return $response;
    }

    /**
     * @param string $countryId
     * @param array $newPickupPoints
     * @throws CouldNotSaveException
     */
    public function disablePickupPoints($countryId, array $newPickupPoints = [])
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(PickupPointInterface::COUNTRY, $countryId)
            ->create();
        $pickupPoints = $this->pickupPointRepository->getList($searchCriteria);

        foreach ($pickupPoints->getItems() as $pickupPoint) {
            $found = false;
            foreach ($newPickupPoints as $newPickupPoint) {
                $apiId = $newPickupPoint['parcelshop_id'] ?? null;
                if ($apiId && $pickupPoint->getApiId() == $apiId) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $pickupPoint->setIsDisabled(true);
                $this->pickupPointRepository->save($pickupPoint);
            }
        }
    }
}
