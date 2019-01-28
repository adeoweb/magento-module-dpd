<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Model\PickupPoint;
use AdeoWeb\Dpd\Model\PickupPointFactory;
use AdeoWeb\Dpd\Model\PickupPointManagement;
use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class PickupPointManagementTest extends AbstractTest
{
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
    private $pickupPointSearchRequestMock;

    /**
     * @var MockObject
     */
    private $carrierService;

    /**
     * @var MockObject
     */
    private $pickupPointFactoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->cacheMock = $this->createMock(\Magento\Framework\App\CacheInterface::class);
        $this->pickupPointRepositoryMock = $this->createMock(\AdeoWeb\Dpd\Api\PickupPointRepositoryInterface::class);
        $this->pickupPointSearchResultsMock = $this->createMock(\AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterface::class);
        $this->searchCriteriaMock = $this->createMock(\Magento\Framework\Api\SearchCriteriaInterface::class);
        $this->pickupPointMock = $this->createMock(PickupPoint::class);
        $this->pickupPointSearchRequestMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequest::class);
        $this->carrierService = $this->createMock(ServiceInterface::class);
        $this->pickupPointFactoryMock = $this->createMock(PickupPointFactory::class);

        $searchCriteriaBuilder = $this->createMock(\AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria\BuilderInterface::class);
        $searchCriteriaBuilder->expects($this->any())
            ->method('build')
            ->willReturn($this->searchCriteriaMock);

        $pickupPointSearchRequestFactoryMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequestFactory::class);
        $pickupPointSearchRequestFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->pickupPointSearchRequestMock);

        $this->subject = $this->objectManager->getObject(PickupPointManagement::class, [
            'cache' => $this->cacheMock,
            'pickupPointRepository' => $this->pickupPointRepositoryMock,
            'searchCriteriaBuilder' => $searchCriteriaBuilder,
            'pickupPointSearchRequestFactory' => $pickupPointSearchRequestFactoryMock,
            'apiService' => $this->carrierService,
            'pickupPointFactory' => $this->pickupPointFactoryMock
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
            ->with('DPD_PICKUP_POINT_LIST_US_TestCity')
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

        $result = $this->subject->getList('US', 'TestCity');
        $expectedResult = [['api_id' => 'LT10848']];

        $this->assertEquals($expectedResult, $result);
    }

    public function testUpdate()
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $this->carrierService->expects($this->atleastOnce())
            ->method('call')
            ->willReturn($responseMock);

        $responseMock->expects($this->atleastOnce())
            ->method('getBody')
            ->with('parcelshops')
            ->willReturn([['parcelshop_id' => 1]]);

        $this->pickupPointFactoryMock->expects($this->atleastOnce())
            ->method('createFromResponseData')
            ->willReturn($this->pickupPointMock);

        $result = $this->subject->update();

        $this->assertTrue($result);
    }
}
