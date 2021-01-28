<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\Data\PickupPointInterfaceFactory;
use AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterface;
use AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterfaceFactory;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use AdeoWeb\Dpd\Model\ResourceModel\PickupPoint as PickupPointResource;
use Magento\Framework\Reflection\DataObjectProcessor;

class PickupPointRepository implements PickupPointRepositoryInterface
{
    /**
     * @var PickupPointInterfaceFactory
     */
    private $pickupPointFactory;

    /**
     * @var ResourceModel\PickupPoint
     */
    private $pickupPointResource;

    /**
     * @var PickupPointSearchResultsInterfaceFactory
     */
    private $pickupPointSearchResultsFactory;

    /**
     * @var PickupPointResource\CollectionFactory
     */
    private $pickupPointCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    public function __construct(
        PickupPointInterfaceFactory $pickupPointFactory,
        PickupPointResource $pickupPointResource,
        PickupPointSearchResultsInterfaceFactory $pickupPointSearchResultsFactory,
        PickupPointResource\CollectionFactory $pickupPointCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->pickupPointFactory = $pickupPointFactory;
        $this->pickupPointResource = $pickupPointResource;
        $this->pickupPointSearchResultsFactory = $pickupPointSearchResultsFactory;
        $this->pickupPointCollectionFactory = $pickupPointCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @param int $pickupPointId
     * @return PickupPointInterface
     * @throws NoSuchEntityException
     */
    public function getById($pickupPointId)
    {
        /** @var PickupPoint $pickupPoint */
        $pickupPoint = $this->pickupPointFactory->create();
        $this->pickupPointResource->load($pickupPoint, $pickupPointId);

        if (!$pickupPoint->getPickupPointId()) {
            throw new NoSuchEntityException(__('Pickup point with id "%1" does not exist.', $pickupPointId));
        }

        return $pickupPoint;
    }

    /**
     * @param int $apiId
     * @return PickupPointInterface
     * @throws NoSuchEntityException
     */
    public function getByApiId($apiId)
    {
        /** @var PickupPoint $pickupPoint */
        $pickupPoint = $this->pickupPointFactory->create();
        $this->pickupPointResource->load($pickupPoint, $apiId, PickupPointInterface::API_ID);

        if (!$pickupPoint->getPickupPointId()) {
            throw new NoSuchEntityException(__('Pickup point with API ID "%1" does not exist.', $apiId));
        }

        return $pickupPoint;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return PickupPointSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->pickupPointSearchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->pickupPointCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        }

        $collection->setOrder('company', SortOrder::SORT_ASC);
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @param PickupPointInterface|PickupPoint $pickupPoint
     * @return PickupPointInterface
     * @throws CouldNotSaveException
     */
    public function save(PickupPointInterface $pickupPoint)
    {
        try {
            $this->pickupPointResource->save($pickupPoint);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $pickupPoint;
    }

    /**
     * @param PickupPointInterface|PickupPoint $pickupPoint
     * @return PickupPointInterface
     * @throws CouldNotDeleteException
     */
    public function delete(PickupPointInterface $pickupPoint)
    {
        try {
            $this->pickupPointResource->delete($pickupPoint);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return $pickupPoint;
    }
}
