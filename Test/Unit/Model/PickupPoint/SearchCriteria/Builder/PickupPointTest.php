<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\PickupPoint\SearchCriteria\Builder;

use AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria\Builder\PickupPoint;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class TypeTest extends AbstractTest
{
    /**
     * @var PickupPoint
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var MockObject
     */
    private $filterBuilderMock;

    /**
     * @var MockObject
     */
    private $sortOrderBuilderMock;

    public function setUp()
    {
        parent::setUp();

        $this->searchCriteriaBuilderMock = $this->createMock(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $this->filterBuilderMock = $this->createMock(\Magento\Framework\Api\FilterBuilder::class);
        $this->sortOrderBuilderMock = $this->createMock(\Magento\Framework\Api\SortOrderBuilder::class);

        $searchCriteriaBuilderFactoryMock = $this->createConfiguredMock(
            \Magento\Framework\Api\SearchCriteriaBuilderFactory::class,
            [
                'create' => $this->searchCriteriaBuilderMock,
            ]
        );
        $filterBuilderFactoryMock = $this->createConfiguredMock(\Magento\Framework\Api\FilterBuilderFactory::class, [
            'create' => $this->filterBuilderMock,
        ]);
        $sortOrderBuilderFactoryMock = $this->createConfiguredMock(
            \Magento\Framework\Api\SortOrderBuilderFactory::class,
            [
                'create' => $this->sortOrderBuilderMock,
            ]
        );

        $this->subject = $this->objectManager->getObject(PickupPoint::class, [
            'searchCriteriaBuilderFactory' => $searchCriteriaBuilderFactoryMock,
            'filterBuilderFactory' => $filterBuilderFactoryMock,
            'sortOrderBuilderFactory' => $sortOrderBuilderFactoryMock,
        ]);
    }

    public function testBuild()
    {
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addSortOrder')
            ->willReturn($this->searchCriteriaBuilderMock);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilters')
            ->willReturn($this->searchCriteriaBuilderMock);

        $result = $this->subject->build([
            'city' => null,
            'country' => 'US',
        ]);
    }
}
