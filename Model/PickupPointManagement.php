<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\PickupPointManagementInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria\BuilderInterface;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\CacheInterface;


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
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var PickupPointUpdater
     */
    private $pickupPointUpdater;

    public function __construct(
        PickupPointRepositoryInterface $pickupPointRepository,
        BuilderInterface $searchCriteriaBuilder,
        CacheInterface $cache,
        PickupPointUpdater $pickupPointUpdater
    ) {
        $this->pickupPointRepository = $pickupPointRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cache = $cache;
        $this->pickupPointUpdater = $pickupPointUpdater;
    }

    /**
     * @param string $country
     * @param string $city
     * @return mixed
     */
    public function getList($country = null, $city = null)
    {
        $context = [
            PickupPointInterface::COUNTRY => $country,
            PickupPointInterface::CITY => $city,
            PickupPointInterface::IS_DISABLED => 0
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

        $this->cache->save(\json_encode($result), $cacheKey, [Block::CACHE_TAG]);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update()
    {
        return $this->pickupPointUpdater->execute();
    }
}
