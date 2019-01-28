<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Location;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Model\Location;
use AdeoWeb\Dpd\Model\Location\DataProvider;
use AdeoWeb\Dpd\Model\ResourceModel\Location\Collection;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Request\DataPersistorInterface;
use PHPUnit\Framework\MockObject\MockObject;

class DataProviderTest extends AbstractTest
{
    /**
     * @var DataProvider
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $locationCollectionMock;

    /**
     * @var MockObject
     */
    private $dataPersistorMock;

    public function setUp()
    {
        parent::setUp();

        $this->locationCollectionMock = $this->createMock(Collection::class);
        $this->dataPersistorMock = $this->createMock(DataPersistorInterface::class);

        $locationCollectionFactoryMock = $this->createConfiguredMock(\AdeoWeb\Dpd\Model\ResourceModel\Location\CollectionFactory::class, [
            'create' => $this->locationCollectionMock
        ]);

        $this->subject = $this->objectManager->getObject(DataProvider::class, [
            'collectionFactory' => $locationCollectionFactoryMock,
            'dataPersistor' => $this->dataPersistorMock
        ]);
    }

    public function testGetData()
    {
        $locationMock = $this->createMock(Location::class);
        $locationMock2 = $this->createMock(Location::class);

        $this->locationCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$locationMock]);

        $locationMock->method('getData')->willReturn(['location_id' => 123]);

        $this->dataPersistorMock->expects($this->once())
            ->method('get')
            ->with('adeoweb_dpd_location')
            ->willReturn(['location_id' => 123]);

        $this->locationCollectionMock->expects($this->once())
            ->method('getNewEmptyItem')
            ->willReturn($locationMock2);

        $this->dataPersistorMock->expects($this->once())
            ->method('clear')
            ->with('adeoweb_dpd_location');

        $locationMock2->expects($this->once())
            ->method('getId')
            ->willReturn(123);

        $result = $this->subject->getData();

        $this->assertArrayHasKey(123, $result);

        $result2 = $this->subject->getData();

        $this->assertArrayHasKey(123, $result2);
    }
}
