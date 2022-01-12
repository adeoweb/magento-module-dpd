<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Controller\Adminhtml\Location\NewAction;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class NewActionTest extends AbstractTest
{
    /**
     * @var NewAction
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $resultMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->resultMock = $this->createMock(\Magento\Framework\Controller\Result\Forward::class);

        $resultFactoryMock = $this->createMock(\Magento\Backend\Model\View\Result\ForwardFactory::class);
        $resultFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultMock);

        $this->subject = $this->objectManager->getObject(NewAction::class, [
            'resultForwardFactory' => $resultFactoryMock
        ]);
    }

    public function testExecute()
    {
        $this->resultMock->expects($this->once())
            ->method('forward')
            ->willReturn($this->resultMock);

        $result = $this->subject->execute();
        $expectedResult = $this->resultMock;

        $this->assertEquals($expectedResult, $result);
    }
}
