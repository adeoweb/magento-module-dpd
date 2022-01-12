<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Helper\Locale\LocaleSortProcessor;
use AdeoWeb\Dpd\Model\PickupPoint;
use AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria\BuilderInterface;
use AdeoWeb\Dpd\Model\PickupPointManagement;
use AdeoWeb\Dpd\Model\PickupPointUpdater;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;

class PickupPointManagementTest extends AbstractTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PickupPointUpdater
     */
    private $pickupPointUpdaterMock;

    /**
     * @var PickupPointManagement
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $cacheMock;

    /**
     * @var MockObject
     */
    private $pickupPointRepositoryMock;

    /**
     * @var MockObject
     */
    private $pickupPointSearchResultsMock;

    /**
     * @var MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var MockObject
     */
    private $pickupPointMock;

    /**
     * @var MockObject
     */
    private $localeSortProcessorMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->cacheMock = $this->createMock(CacheInterface::class);
        $this->pickupPointRepositoryMock = $this->createMock(PickupPointRepositoryInterface::class);
        $this->searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $this->pickupPointMock = $this->createMock(PickupPoint::class);
        $this->pickupPointUpdaterMock = $this->createMock(PickupPointUpdater::class);
        $this->pickupPointSearchResultsMock = $this->createMock(PickupPointSearchResultsInterface::class);
        $this->localeSortProcessorMock = $this->createMock(LocaleSortProcessor::class);

        $searchCriteriaBuilder = $this->createMock(BuilderInterface::class);
        $searchCriteriaBuilder->expects($this->any())
            ->method('build')
            ->willReturn($this->searchCriteriaMock);

        $this->subject = $this->objectManager->getObject(PickupPointManagement::class, [
            'cache' => $this->cacheMock,
            'pickupPointUpdater' => $this->pickupPointUpdaterMock,
            'pickupPointRepository' => $this->pickupPointRepositoryMock,
            'searchCriteriaBuilder' => $searchCriteriaBuilder,
            'localeSortProcessor' => ['LT' => $this->localeSortProcessorMock]
        ]);
    }

    public function testGetListWithCache()
    {
        $this->cacheMock->expects($this->once())
            ->method('load')
            ->with('DPD_PICKUP_POINT_LIST_US_TestCity')
            ->willReturn('[{"api_id":"LT10848"}]');

        $result = $this->subject->getList('US', 'TestCity');
        $expectedResult = [['api_id' => 'LT10848']];

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetList()
    {
        $this->cacheMock->expects($this->once())
            ->method('load')
            ->with('DPD_PICKUP_POINT_LIST_LT_TestCity')
            ->willReturn(null);

        $this->pickupPointRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->pickupPointSearchResultsMock);

        $this->pickupPointSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->pickupPointMock]);

        $this->pickupPointMock->expects($this->once())
            ->method('toArray')
            ->willReturn(['api_id' => 'LT10848']);

        $this->localeSortProcessorMock->expects($this->once())
            ->method('sortData')
            ->willReturn([['api_id' => 'LT10848']]);

        $result = $this->subject->getList('LT', 'TestCity');
        $expectedResult = [['api_id' => 'LT10848']];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function testUpdate()
    {
        $this->pickupPointUpdaterMock->expects($this->once())->method('execute');

        $this->subject->update();

        $this->assertTrue(true);
    }
}
