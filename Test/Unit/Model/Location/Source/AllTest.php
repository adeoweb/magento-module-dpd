<?php

namespace Model\Location\Source;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\Data\LocationSearchResultsInterface;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Model\Location\Source\All;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class AllTest extends AbstractTest
{
    /**
     * @var All
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $locationRepositoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->locationRepositoryMock = $this->createMock(LocationRepositoryInterface::class);

        $this->subject = $this->objectManager->getObject(All::class, [
            'locationRepository' => $this->locationRepositoryMock
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
                'label' => '--Select a Location--',
                'value' => ''
            ],
            [
                'label' => 'TestName',
                'value' => '1'
            ]
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
