<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterface;
use AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterfaceFactory;
use AdeoWeb\Dpd\Model\PickupPoint;
use AdeoWeb\Dpd\Model\PickupPointFactory;
use AdeoWeb\Dpd\Model\PickupPointRepository;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;

class PickupPointRepositoryTest extends AbstractTest
{
    /**
     * @var PickupPointRepository
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $pickupPointModelMock;

    /**
     * @var MockObject
     */
    private $resourceMock;

    /**
     * @var MockObject
     */
    private $pickupPointSearchResultsInterfaceMock;

    /**
     * @var MockObject
     */
    private $pickupPointCollectionMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->pickupPointModelMock = $this->createMock(PickupPoint::class);
        $this->resourceMock = $this->createMock(\AdeoWeb\Dpd\Model\ResourceModel\PickupPoint::class);
        $this->pickupPointSearchResultsInterfaceMock = $this->createMock(
            \AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterface::class
        );
        $this->pickupPointCollectionMock = $this->createMock(
            \AdeoWeb\Dpd\Model\ResourceModel\PickupPoint\Collection::class
        );

        $pickupPointSearchResultsInterfaceFactoryMock = $this->createMock(
            PickupPointSearchResultsInterfaceFactory::class
        );
        $pickupPointSearchResultsInterfaceFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->pickupPointSearchResultsInterfaceMock);

        $pickupPointFactoryMock = $this->createMock(\AdeoWeb\Dpd\Api\Data\PickupPointInterfaceFactory::class);
        $pickupPointFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->pickupPointModelMock);

        $pickupPointCollectionFactory = $this->createMock(
            \AdeoWeb\Dpd\Model\ResourceModel\PickupPoint\CollectionFactory::class
        );
        $pickupPointCollectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->pickupPointCollectionMock);

        $this->subject = $this->objectManager->getObject(PickupPointRepository::class, [
            'pickupPointFactory' => $pickupPointFactoryMock,
            'pickupPointResource' => $this->resourceMock,
            'pickupPointCollectionFactory' => $pickupPointCollectionFactory,
            'pickupPointSearchResultsFactory' => $pickupPointSearchResultsInterfaceFactoryMock,
        ]);
    }

    public function testSaveWithException()
    {
        $pickupPointMock = $this->createMock(PickupPoint::class);

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($this->pickupPointModelMock)
            ->willThrowException(new \Exception('Could not save'));

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Could not save');

        return $this->subject->save($pickupPointMock);
    }

    public function testSave()
    {
        $pickupPointMock = $this->createMock(PickupPoint::class);

        $result = $this->subject->save($pickupPointMock);

        $this->assertInstanceOf(PickupPointInterface::class, $result);
    }

    public function testGetByIdWithException()
    {
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($this->pickupPointModelMock, 1)
            ->willReturn($this->pickupPointModelMock);

        $this->pickupPointModelMock->expects($this->once())
            ->method('getPickupPointId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('Pickup point with id "1" does not exist.');

        return $this->subject->getById(1);
    }

    public function testGetById()
    {
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($this->pickupPointModelMock, 1)
            ->willReturn($this->pickupPointModelMock);

        $this->pickupPointModelMock->expects($this->once())
            ->method('getPickupPointId')
            ->willReturn(1);

        $this->subject->getById(1);
    }

    public function testGetByApiIdWithException()
    {
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($this->pickupPointModelMock, 1, PickupPointInterface::API_ID)
            ->willReturn($this->pickupPointModelMock);

        $this->pickupPointModelMock->expects($this->once())
            ->method('getPickupPointId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('Pickup point with API ID "1" does not exist.');

        return $this->subject->getByApiId(1);
    }

    public function testGetByApiId()
    {
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($this->pickupPointModelMock, 1, PickupPointInterface::API_ID)
            ->willReturn($this->pickupPointModelMock);

        $this->pickupPointModelMock->expects($this->once())
            ->method('getPickupPointId')
            ->willReturn(1);

        $this->subject->getByApiId(1);
    }

    public function testGetList()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $this->pickupPointCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->pickupPointModelMock]);

        $filterGroupMock = $this->createMock(FilterGroup::class);
        $filterMock = $this->createMock(Filter::class);
        $sortOrderMock = $this->createMock(SortOrder::class);

        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);

        $searchCriteriaMock->expects($this->once())
            ->method('getSortOrders')
            ->willReturn([$sortOrderMock]);

        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterMock]);

        $result = $this->subject->getList($searchCriteriaMock);

        $this->assertInstanceOf(PickupPointSearchResultsInterface::class, $result);
    }

    public function testDeleteWithException()
    {
        $pickupPointMock = $this->createMock(PickupPoint::class);

        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($this->pickupPointModelMock)
            ->willThrowException(new \Exception('Error while deleting'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error while deleting');

        return $this->subject->delete($pickupPointMock);
    }

    public function testDelete()
    {
        $pickupPointMock = $this->createMock(PickupPoint::class);

        $result = $this->subject->delete($pickupPointMock);

        $this->assertInstanceOf(PickupPointInterface::class, $result);
    }
}
