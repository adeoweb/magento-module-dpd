<?php

namespace Model\Location\Source;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\Data\LocationSearchResultsInterface;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Model\Location\Source\Warehouse;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class WarehouseTest extends AbstractTest
{
    /**
     * @var Warehouse
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $locationRepositoryMock;

    /**
     * @var MockObject
     */
    private $searchCriteriaBuilderMock;

    public function setUp()
    {
        parent::setUp();

        $this->locationRepositoryMock = $this->createMock(LocationRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(\Magento\Framework\Api\SearchCriteriaBuilder::class);

        $searchCriteriaBuilderFactory = $this->createConfiguredMock(\Magento\Framework\Api\SearchCriteriaBuilderFactory::class,
            [
                'create' => $this->searchCriteriaBuilderMock,
            ]);

        $this->subject = $this->objectManager->getObject(Warehouse::class, [
            'locationRepository' => $this->locationRepositoryMock,
            'searchCriteriaBuilderFactory' => $searchCriteriaBuilderFactory,
        ]);
    }

    public function testToOptionArray()
    {
        $locationMock = $this->createMock(LocationInterface::class);

        $locationSearchResultsMock = $this->createMock(LocationSearchResultsInterface::class);
        $locationSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$locationMock]);

        $this->locationRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($locationSearchResultsMock);

        $locationMock->expects($this->once())
            ->method('getName')
            ->willReturn('TestName');
        $locationMock->expects($this->once())
            ->method('getLocationId')
            ->willReturn('1');

        $result = $this->subject->toOptionArray();
        $expectedResult = [
            [
                'label' => '--Select a Warehouse--',
                'value' => '',
            ],
            [
                'label' => 'TestName',
                'value' => '1',
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }
}

