<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Controller\Adminhtml\Location\MassDelete;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class MassDeleteTest extends AbstractTest
{
    /**
     * @var MassDelete
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $filterMock;

    /**
     * @var MockObject
     */
    private $collectionMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->filterMock = $this->createMock(\Magento\Ui\Component\MassAction\Filter::class);
        $this->collectionMock = $this->createMock(\AdeoWeb\Dpd\Model\ResourceModel\Location\Collection::class);

        $collectionFactoryMock = $this->createConfiguredMock(
            \AdeoWeb\Dpd\Model\ResourceModel\Location\CollectionFactory::class,
            ['create' => $this->collectionMock]
        );

        $resultForward = $this->createMock(\Magento\Backend\Model\View\Result\Forward::class);

        $resultForwardFactory = $this->createConfiguredMock(\Magento\Backend\Model\View\Result\ForwardFactory::class, [
            'create' => $resultForward
        ]);

        $this->subject = $this->objectManager->getObject(MassDelete::class, [
            'filter' => $this->filterMock,
            'collectionFactory' => $collectionFactoryMock,
            'resultForwardFactory' => $resultForwardFactory
        ]);
    }

    public function testExecute()
    {
        $locationMock = $this->createMock(LocationInterface::class);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->collectionMock);

        $this->collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$locationMock]);

        $this->subject->execute();
        $this->assertTrue(true);
    }
}
