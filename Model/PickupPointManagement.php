<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\PickupPointManagementInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria\BuilderInterface;
use AdeoWeb\Dpd\Model\Provider\PickupPoint\AllowedCountries;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequest;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use AdeoWeb\Dpd\Model\PickupPoint\TableMaintainer;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

class PickupPointManagement implements PickupPointManagementInterface
{
    const CACHE_KEY = 'DPD_PICKUP_POINT_LIST_%s_%s';

    /**
     * @var PickupPointRepositoryInterface
     */
    private $pickupPointRepository;

    /**
     * @var BuilderInterface
     */
    private $searchCriteriaBuilder;

    /**
     * @var TableMaintainer
     */
    private $tableMaintainer;

    /**
     * @var ServiceInterface
     */
    private $apiService;

    /**
     * @var PickupPointSearchRequestFactory
     */
    private $pickupPointSearchRequestFactory;

    /**
     * @var PickupPointFactory
     */
    private $pickupPointFactory;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Provider\PickupPoint\AllowedCountries
     */
    private $pickupAllowedCountriesProvider;

    public function __construct(
        PickupPointRepositoryInterface $pickupPointRepository,
        BuilderInterface $searchCriteriaBuilder,
        TableMaintainer $tableMaintainer,
        ServiceInterface $apiService,
        PickupPointSearchRequestFactory $pickupPointSearchRequestFactory,
        PickupPointFactory $pickupPointFactory,
        CacheInterface $cache,
        AllowedCountries $pickupAllowedCountriesProvider
    ) {
        $this->pickupPointRepository = $pickupPointRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->tableMaintainer = $tableMaintainer;
        $this->apiService = $apiService;
        $this->pickupPointSearchRequestFactory = $pickupPointSearchRequestFactory;
        $this->pickupPointFactory = $pickupPointFactory;
        $this->cache = $cache;
        $this->pickupAllowedCountriesProvider = $pickupAllowedCountriesProvider;
    }

    /**
     * @param string $country
     * @param string $city
     * @return mixed
     */
    public function getList($country = null, $city = null)
    {
        $context = [
            'country' => $country,
            'city' => $city
        ];

        $cacheKey = \sprintf(self::CACHE_KEY, $country, $city);

        if ($cachedResult = $this->cache->load($cacheKey)) {
            return \json_decode($cachedResult, true);
        }

        $searchCriteria = $this->searchCriteriaBuilder->build($context);

        $items = $this->pickupPointRepository->getList($searchCriteria)->getItems();

        $result = [];

        foreach ($items as $item) {
            $result[] = $item->toArray();
        }

        $this->cache->save(\json_encode($result), $cacheKey);

        return $result;
    }

    /**
     * @return bool|array
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function update()
    {
        $this->tableMaintainer->resetTable();

        $result = [];

        foreach ($this->getAllowedPickupPointCountries() as $countryCode) {
            /** @var PickupPointSearchRequest $request */
            $request = $this->pickupPointSearchRequestFactory->create();
            $request->setCountry($countryCode);
            $request->setFetchAllByCountryFlag(true);

            $pickupPointListResponse = $this->apiService->call($request);

            if ($pickupPointListResponse->hasError()) {
                $result[$countryCode] = $pickupPointListResponse->getErrorMessage();
                continue;
            }

            $response = $pickupPointListResponse->getBody('parcelshops');

            if (!\is_array($response)) {
                continue;
            }

            foreach ($response as $pickupPointData) {
                $pickupPoint = $this->pickupPointFactory->createFromResponseData($pickupPointData);

                $this->pickupPointRepository->save($pickupPoint);
            }
        }

        return $result ?: true;
    }

    /**
     * @return array
     */
    private function getAllowedPickupPointCountries()
    {
        return $this->pickupAllowedCountriesProvider->get();
    }
}