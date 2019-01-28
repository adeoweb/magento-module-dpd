<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\Data\LocationSearchResultsInterface;
use AdeoWeb\Dpd\Api\Data\LocationSearchResultsInterfaceFactory;
use AdeoWeb\Dpd\Model\Location;
use AdeoWeb\Dpd\Model\LocationFactory;
use AdeoWeb\Dpd\Model\LocationRepository;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;

class LocationRepositoryTest extends AbstractTest
{
    /**
     * @var LocationRepository
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $locationModelMock;

    /**
     * @var MockObject
     */
    private $resourceMock;

    /**
     * @var MockObject
     */
    private $locationSearchResultsInterfaceMock;

    /**
     * @var MockObject
     */
    private $locationCollectionMock;

    public function setUp()
    {
        parent::setUp();

        $this->locationModelMock = $this->createMock(Location::class);
        $this->resourceMock = $this->createMock(\AdeoWeb\Dpd\Model\ResourceModel\Location::class);
        $this->locationSearchResultsInterfaceMock = $this->createMock(\AdeoWeb\Dpd\Api\Data\LocationSearchResultsInterface::class);
        $this->locationCollectionMock = $this->createMock(\AdeoWeb\Dpd\Model\ResourceModel\Location\Collection::class);

        $locationSearchResultsInterfaceFactoryMock = $this->createMock(LocationSearchResultsInterfaceFactory::class);
        $locationSearchResultsInterfaceFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->locationSearchResultsInterfaceMock);

        $locationFactoryMock = $this->createMock(LocationFactory::class);
        $locationFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->locationModelMock);

        $locationCollectionFactory = $this->createMock(\AdeoWeb\Dpd\Model\ResourceModel\Location\CollectionFactory::class);
        $locationCollectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->locationCollectionMock);

        $this->subject = $this->objectManager->getObject(LocationRepository::class, [
            'locationFactory' => $locationFactoryMock,
            'resource' => $this->resourceMock,
            'locationCollectionFactory' => $locationCollectionFactory,
            'locationSearchResultsInterfaceFactory' => $locationSearchResultsInterfaceFactoryMock,
        ]);
    }

    public function testSaveWithException()
    {
        $locationMock = $this->createMock(LocationInterface::class);

        $this->locationModelMock->expects($this->once())
            ->method('setData')
            ->willReturn($this->locationModelMock);

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($this->locationModelMock)
            ->willThrowException(new \Exception('Could not save'));

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Could not save');

        return $this->subject->save($locationMock);
    }

    public function testSave()
    {
        $locationMock = $this->createMock(LocationInterface::class);

        $this->locationModelMock->expects($this->once())
            ->method('setData')
            ->willReturn($this->locationModelMock);

        $this->locationModelMock->expects($this->once())
            ->method('getDataModel');

        $this->subject->save($locationMock);
    }

    public function testGetByIdWithException()
    {
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($this->locationModelMock, 1)
            ->willReturn($this->locationModelMock);

        $this->locationModelMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('Location with id "1" does not exist.');

        return $this->subject->getById(1);
    }

    public function testGetById()
    {
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($this->locationModelMock, 1)
            ->willReturn($this->locationModelMock);

        $this->locationModelMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->locationModelMock->expects($this->once())
            ->method('getDataModel');

        $this->subject->getById(1);
    }

    public function testGetListWithoutSearchCriteria()
    {
        $this->locationCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->locationModelMock]);

        $result = $this->subject->getList(null);

        $this->assertInstanceOf(LocationSearchResultsInterface::class, $result);
    }

    public function testGetList()
    {
        $searchCriteriaMock = $this->createMock(\Magento\Framework\Api\SearchCriteriaInterface::class);

        $this->locationCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->locationModelMock]);

        $filterGroupMock = $this->createMock(\Magento\Framework\Api\Search\FilterGroup::class);
        $filterMock = $this->createMock(\Magento\Framework\Api\Filter::class);
        $sortOrderMock = $this->createMock(\Magento\Framework\Api\SortOrder::class);

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

        $this->assertInstanceOf(LocationSearchResultsInterface::class, $result);
    }

    public function testDeleteWithException()
    {
        $locationMock = $this->createMock(\AdeoWeb\Dpd\Api\Data\LocationInterface::class);

        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($this->locationModelMock)
            ->willThrowException(new \Exception('Error while deleting'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error while deleting');

        return $this->subject->delete($locationMock);
    }

    public function testDelete()
    {
        $locationMock = $this->createMock(\AdeoWeb\Dpd\Api\Data\LocationInterface::class);

        $this->assertTrue($this->subject->delete($locationMock));
    }
}
