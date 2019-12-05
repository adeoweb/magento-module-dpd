<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Model\PickupPoint\CountryService;
use AdeoWeb\Dpd\Model\Provider\PickupPoint\AllowedCountries;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class PickupPointUpdater
{
    /**
     * @var PickupPointRepositoryInterface
     */
    private $pickupPointRepository;

    /**
     * @var PickupPointFactory
     */
    private $pickupPointFactory;

    /**
     * @var AllowedCountries
     */
    private $pickupAllowedCountriesProvider;

    /**
     * @var CountryService
     */
    private $countryService;

    public function __construct(
        PickupPointRepositoryInterface $pickupPointRepository,
        PickupPointFactory $pickupPointFactory,
        CountryService $countryService,
        AllowedCountries $pickupAllowedCountriesProvider
    ) {
        $this->pickupPointRepository = $pickupPointRepository;
        $this->pickupPointFactory = $pickupPointFactory;
        $this->pickupAllowedCountriesProvider = $pickupAllowedCountriesProvider;
        $this->countryService = $countryService;
    }

    /**
     * @return array|bool
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute()
    {
        $result = [];

        $countryCodes = $this->pickupAllowedCountriesProvider->get();
        foreach ($countryCodes as $countryCode) {
            $response = $this->updateCountryPickupPoints($countryCode);
            if ($response !== true) {
                $result[$countryCode] = $response;
            }
        }

        return $result ?: true;
    }

    /**
     * @param $countryCode
     * @return array|bool|null|string
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function updateCountryPickupPoints($countryCode)
    {
        try {
            $servicePickupPoints = $this->countryService->getPickupPoints($countryCode);
        } catch (LocalizedException $up) {
            return $up->getMessage();
        }

        foreach ($servicePickupPoints as $pickupPointData) {
            $this->updatePickupPoint($pickupPointData);
        }

        $this->countryService->disablePickupPoints($countryCode, $servicePickupPoints);

        return true;
    }

    /**
     * @param array $pickupPointData
     * @throws CouldNotSaveException
     */
    private function updatePickupPoint($pickupPointData)
    {
        $newPickupPoint = $this->pickupPointFactory->createFromResponseData($pickupPointData);

        try {
            $pickupPoint = $this->pickupPointRepository->getByApiId($newPickupPoint->getApiId());
            $pickupPoint->addData($newPickupPoint->getData());
        } catch (NoSuchEntityException $up) {
            $pickupPoint = $this->pickupPointFactory->create($newPickupPoint->getData());
        }

        $this->pickupPointRepository->save($pickupPoint);
    }
}
