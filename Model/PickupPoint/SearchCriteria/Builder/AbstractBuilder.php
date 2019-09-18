<?php

namespace AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria\Builder;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrderBuilderFactory;

abstract class AbstractBuilder
{
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var SortOrderBuilderFactory
     */
    private $sortOrderBuilderFactory;

    /**
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;

    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory,
        FilterBuilderFactory $filterBuilderFactory
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilderFactory = $sortOrderBuilderFactory;
        $this->filterBuilderFactory = $filterBuilderFactory;
    }

    /**
     * @return SearchCriteriaInterface
     */
    abstract public function build();

    /**
     * @return SearchCriteriaBuilder
     */
    protected function getInstance()
    {
        return $this->searchCriteriaBuilderFactory->create();
    }

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param $field
     * @param string $direction
     * @return SearchCriteriaBuilder
     */
    protected function addSortField(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        $field,
        $direction = SortOrder::SORT_DESC
    ) {
        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->sortOrderBuilderFactory->create();
        $sortOrderBuilder->setField($field);
        $sortOrderBuilder->setDirection($direction);

        return $searchCriteriaBuilder->addSortOrder($sortOrderBuilder->create());
    }

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param $field
     * @param $value
     * @return SearchCriteriaBuilder
     */
    protected function addFilter(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        $field,
        $value
    ) {
        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = $this->filterBuilderFactory->create();
        $filterBuilder->setField($field);
        $filterBuilder->setValue($value);

        return $searchCriteriaBuilder->addFilters([$filterBuilder->create()]);
    }
}
