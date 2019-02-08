<?php

namespace AdeoWeb\Dpd\Api;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\Data\LocationSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface LocationRepositoryInterface
{
    /**
     * @param LocationInterface $location
     * @return LocationInterface
     * @throws LocalizedException
     */
    public function save($location);

    /**
     * @param string $locationId
     * @return LocationInterface
     * @throws LocalizedException
     */
    public function getById($locationId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return LocationSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param LocationInterface $location
     * @return boolean
     * @throws LocalizedException
     */
    public function delete($location);

    /**
     * @param string $locationId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($locationId);
}
