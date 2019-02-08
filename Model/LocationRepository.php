<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use AdeoWeb\Dpd\Api\Data\LocationSearchResultsInterfaceFactory;
use AdeoWeb\Dpd\Model\ResourceModel\Location as ResourceLocation;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use AdeoWeb\Dpd\Model\ResourceModel\Location\CollectionFactory as LocationCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @var ResourceLocation
     */
    protected $resource;

    /**
     * @var LocationFactory
     */
    protected $locationFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var LocationCollectionFactory
     */
    protected $locationCollectionFactory;

    /**
     * @var LocationSearchResultsInterfaceFactory
     */
    private $locationSearchResultsInterfaceFactory;

    public function __construct(
        ResourceLocation $resource,
        LocationFactory $locationFactory,
        LocationCollectionFactory $locationCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        LocationSearchResultsInterfaceFactory $locationSearchResultsInterfaceFactory
    ) {
        $this->resource = $resource;
        $this->locationFactory = $locationFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->locationSearchResultsInterfaceFactory = $locationSearchResultsInterfaceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save($location)
    {
        $locationData = $this->extensibleDataObjectConverter->toNestedArray(
            $location,
            [],
            LocationInterface::class
        );

        $locationModel = $this->locationFactory->create()->setData($locationData);

        try {
            $this->resource->save($locationModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the location: %1',
                $exception->getMessage()
            ));
        }
        return $locationModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($locationId)
    {
        $location = $this->locationFactory->create();

        $this->resource->load($location, $locationId);

        if (!$location->getId()) {
            throw new NoSuchEntityException(__('Location with id "%1" does not exist.', $locationId));
        }

        return $location->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        $searchResults = $this->locationSearchResultsInterfaceFactory->create();
        $collection = $this->locationCollectionFactory->create();

        if ($searchCriteria) {
            $searchResults->setSearchCriteria($searchCriteria);

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

            $collection->setCurPage($searchCriteria->getCurrentPage());
            $collection->setPageSize($searchCriteria->getPageSize());
        }

        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($location)
    {
        try {
            $locationModel = $this->locationFactory->create();
            $this->resource->load($locationModel, $location->getLocationId());
            $this->resource->delete($locationModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Location: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function deleteById($locationId)
    {
        return $this->delete($this->getById($locationId));
    }
}
