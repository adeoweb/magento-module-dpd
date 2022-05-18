<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Action;

use AdeoWeb\Dpd\Controller\Adminhtml\Action\CollectionRequest;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;

class CollectionRequestTest extends AbstractTest
{
    /**
     * @var CollectionRequest
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $requestMock;

    /**
     * @var MockObject
     */
    private $collectionRequestManagementMock;

    /**
     * @var MockObject
     */
    private $jsonMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestMock = $this->createMock(\Magento\Framework\App\Request\Http::class);

        $contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $contextMock->expects($this->atLeastOnce())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->collectionRequestManagementMock = $this->createMock(
            \AdeoWeb\Dpd\Api\CollectionRequestManagementInterface::class
        );

        $this->jsonMock = $this->createMock(\Magento\Framework\Controller\Result\Json::class);

        $jsonFactoryMock = $this->createMock(\Magento\Framework\Controller\Result\JsonFactory::class);
        $jsonFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->jsonMock);

        $this->subject = $this->objectManager->getObject(CollectionRequest::class, [
            'context' => $contextMock,
            'collectionRequestManagement' => $this->collectionRequestManagementMock,
            'resultJsonFactory' => $jsonFactoryMock
        ]);
    }

    public function testExecuteWithNonAjaxCall()
    {
        $this->requestMock->expects($this->atleastOnce())
            ->method('isXmlHttpRequest')
            ->willReturn(false);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Only AJAX calls are permitted');

        return $this->subject->execute();
    }

    public function testExecuteWithException()
    {
        $this->requestMock->expects($this->atleastOnce())
            ->method('isXmlHttpRequest')
            ->willReturn(true);

        $this->requestMock->expects($this->atleastOnce())
            ->method('getParams')
            ->willReturn(['test']);

        $this->collectionRequestManagementMock->expects($this->atleastOnce())
            ->method('collectionRequest')
            ->willThrowException(new \Exception('Incorrect data!'));

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with(['error' => true, 'message' => 'Incorrect data!']);

        $this->subject->execute();

        $this->assertTrue(true);
    }

    public function testExecute()
    {
        $this->requestMock->expects($this->atleastOnce())
            ->method('isXmlHttpRequest')
            ->willReturn(true);

        $this->requestMock->expects($this->atleastOnce())
            ->method('getParams')
            ->willReturn(['test']);

        $this->collectionRequestManagementMock->expects($this->atleastOnce())
            ->method('collectionRequest')
            ->willReturn(true);

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with(['error' => false, 'message' => __('DPD Collection Request successfully called')]);

        $this->subject->execute();

        $this->assertTrue(true);
    }
}
