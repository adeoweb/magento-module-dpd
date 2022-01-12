<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Config;

use AdeoWeb\Dpd\Model\Config\CallCourierOrderCount;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Config\Model\ResourceModel\Config\Data\Collection;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;

class CallCourierOrderCountTest extends AbstractTest
{
    /**
     * @var CallCourierOrderCount
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $configCollectionMock;

    /**
     * @var MockObject
     */
    private $configWriterMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->configCollectionMock = $this->createMock(Collection::class);
        $this->configWriterMock = $this->createMock(\Magento\Framework\App\Config\Storage\WriterInterface::class);

        $configCollectionFactoryMock = $this->createConfiguredMock(CollectionFactory::class, [
            'create' => $this->configCollectionMock,
        ]);

        $this->subject = $this->objectManager->getObject(CallCourierOrderCount::class, [
            'configCollectionFactory' => $configCollectionFactoryMock,
            'configWriter' => $this->configWriterMock
        ]);
    }

    public function testGetWithEmptyConfig()
    {
        $this->assertEquals(1, $this->subject->get());
    }

    public function testGet()
    {
        $configMock = $this->createPartialMock(DataObject::class, []);

        $configMock->setValue(2);

        $this->configCollectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($configMock);

        $this->assertEquals(2, $this->subject->get());
    }

    public function testRegister()
    {
        $configMock = $this->createPartialMock(DataObject::class, []);

        $configMock->setValue(2);

        $this->configCollectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($configMock);

        $this->configWriterMock->expects($this->once())
            ->method('save')
            ->with('carriers/dpd/call_courier_order_count', 3);

        $this->assertEquals(2, $this->subject->register());
    }
}
