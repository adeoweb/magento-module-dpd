<?php

namespace AdeoWeb\Dpd\Api;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\Data\LocationSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface LocationRepositoryInterface
{
    /**
     * Public method
     *
     * @param LocationInterface $location
     * @return LocationInterface
     * @throws LocalizedException
     */
    public function save($location);

    /**
     * Public method
     *
     * @param string $locationId
     * @return LocationInterface
     * @throws LocalizedException
     */
    public function getById($locationId);

    /**
     * Public method
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return LocationSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);

    /**
     * Public method
     *
     * @param LocationInterface $location
     * @return boolean
     * @throws LocalizedException
     */
    public function delete($location);

    /**
     * Public method
     *
     * @param string $locationId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($locationId);
}
