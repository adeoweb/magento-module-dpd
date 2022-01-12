<?php

namespace Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Controller\Adminhtml\Location\InlineEdit;
use AdeoWeb\Dpd\Model\Location;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;

class InlineEditTest extends AbstractTest
{
    /**
     * @var InlineEdit
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $requestMock;

    /**
     * @var MockObject
     */
    private $jsonMock;

    /**
     * @var MockObject
     */
    private $locationRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestMock = $this->createMock(Http::class);

        $contextMock = $this->objectManager->getObject(\Magento\Backend\App\Action\Context::class, [
            'request' => $this->requestMock
        ]);

        $this->jsonMock = $this->createMock(\Magento\Framework\Controller\Result\Json::class);

        $jsonFactoryMock = $this->createMock(\Magento\Framework\Controller\Result\JsonFactory::class);
        $jsonFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->jsonMock);

        $this->locationRepositoryMock = $this->createMock(LocationRepositoryInterface::class);

        $this->subject = $this->objectManager->getObject(InlineEdit::class, [
            'context' => $contextMock,
            'jsonFactory' => $jsonFactoryMock,
            'locationRepository' => $this->locationRepositoryMock
        ]);
    }

    public function testExecuteWithNonAjaxCall()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Only AJAX calls are permitted');

        return $this->subject->execute();
    }

    public function testExecuteWithNoRequestData()
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->withConsecutive(['isAjax'], ['items'])
            ->willReturnOnConsecutiveCalls(true, []);

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [__('Please correct the data sent.')],
                'error' => true
            ]);

        return $this->subject->execute();
    }

    public function testExecuteWithLocationException()
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->withConsecutive(['isAjax'], ['items'])
            ->willReturnOnConsecutiveCalls(true, [1 => []]);

        $this->locationRepositoryMock->expects($this->atLeastOnce())
            ->method('getById')
            ->with(1)
            ->WillThrowException(new \Exception('No such entity'));

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => ['[Location ID: 1] No such entity'],
                'error' => true
            ]);

        return $this->subject->execute();
    }

    public function testExecute()
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->withConsecutive(['isAjax'], ['items'])
            ->willReturnOnConsecutiveCalls(true, [1 => []]);

        $locationMock = $this->objectManager->getObject(Location::class);

        $this->locationRepositoryMock->expects($this->atLeastOnce())
            ->method('getById')
            ->with(1)
            ->willReturn($locationMock);

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [],
                'error' => false
            ]);

        return $this->subject->execute();
    }
}
