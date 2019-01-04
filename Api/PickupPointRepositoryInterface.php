<?php

namespace AdeoWeb\Dpd\Api;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface PickupPointRepositoryInterface
{
    /**
     * @param int $pickupPointId
     * @return PickupPointInterface
     * @throws NoSuchEntityException
     */
    public function getById($pickupPointId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return PickupPointSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param PickupPointInterface $pickupPoint
     * @return PickupPointInterface
     * @throws CouldNotSaveException
     */
    public function save(PickupPointInterface $pickupPoint);

    /**
     * @param PickupPointInterface $pickupPoint
     * @return PickupPointInterface
     * @throws CouldNotDeleteException
     */
    public function delete(PickupPointInterface $pickupPoint);
}