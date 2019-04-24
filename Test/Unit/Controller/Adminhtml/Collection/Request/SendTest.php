<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Collection\Request;

use AdeoWeb\Dpd\Controller\Adminhtml\Collection\Request\Send;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;

class SendTest extends AbstractTest
{
    /**
     * @var Send
     */
    private $subject;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $resultRedirectMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $collectionRequestManagementMock;

    public function setUp()
    {
        parent::setUp();

        $this->resultRedirectMock = $this->createMock(Redirect::class);
        $resultRedirectFactoryMock = $this->createConfiguredMock(RedirectFactory::class, [
            'create' => $this->resultRedirectMock,
        ]);

        $this->requestMock = $this->createMock(\Magento\Framework\App\Request\Http::class);

        $messageManagerMock = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);

        $contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($messageManagerMock);

        $this->collectionRequestManagementMock = $this->createMock(\AdeoWeb\Dpd\Api\CollectionRequestManagementInterface::class);

        $this->subject = $this->objectManager->getObject(Send::class, [
            'resultRedirectFactory' => $resultRedirectFactoryMock,
            'context' => $contextMock,
            'collectionRequestManagement' => $this->collectionRequestManagementMock
        ]);
    }

    public function testExecuteWithNonPostMethod()
    {
        $this->requestMock->method('isPost')->willReturn(false);

        $this->resultRedirectMock->expects($this->once())->method('setPath')
            ->with('*/*/index')
            ->willReturn($this->resultRedirectMock);

        $this->assertInstanceOf(Redirect::class, $this->subject->execute());
    }

    public function testExecuteWithCollectionRequestException()
    {
        $this->requestMock->method('isPost')->willReturn(true);
        $this->requestMock->method('getParams')->willReturn([]);

        $this->collectionRequestManagementMock->method('collectionRequest')->willThrowException(new \Exception());

        $this->resultRedirectMock->expects($this->once())->method('setPath')
            ->with('*/*/index')
            ->willReturn($this->resultRedirectMock);

        $this->assertInstanceOf(Redirect::class, $this->subject->execute());
    }

    public function testExecute()
    {
        $this->requestMock->method('isPost')->willReturn(true);
        $this->requestMock->method('getParams')->willReturn([]);

        $this->resultRedirectMock->expects($this->once())->method('setPath')
            ->with('sales/order/index')
            ->willReturn($this->resultRedirectMock);

        $this->assertInstanceOf(Redirect::class, $this->subject->execute());
    }
}
