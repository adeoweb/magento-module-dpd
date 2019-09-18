<?php

namespace Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Controller\Adminhtml\Location\Delete;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class DeleteTest extends AbstractTest
{
    /**
     * @var Delete
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $redirectMock;

    /**
     * @var MockObject
     */
    private $requestMock;

    /**
     * @var MockObject
     */
    private $messageManagerMock;

    /**
     * @var MockObject
     */
    private $responseMock;

    /**
     * @var MockObject
     */
    private $locationRepositoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->requestMock = $this->createMock(Http::class);
        $this->responseMock = $this->createMock(\Magento\Framework\App\Response\Http::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);

        $this->redirectMock = $this->createMock(\Magento\Backend\Model\View\Result\Redirect::class);

        $redirectFactoryMock = $this->createMock(\Magento\Backend\Model\View\Result\RedirectFactory::class);
        $redirectFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->redirectMock);

        $contextMock = $this->objectManager->getObject(\Magento\Backend\App\Action\Context::class, [
            'request' => $this->requestMock,
            'response' => $this->responseMock,
            'resultRedirectFactory' => $redirectFactoryMock,
            'messageManager' => $this->messageManagerMock
        ]);

        $this->locationRepositoryMock = $this->createMock(\AdeoWeb\Dpd\Api\LocationRepositoryInterface::class);

        $this->subject = $this->objectManager->getObject(Delete::class, [
            'context' => $contextMock,
            'locationRepository' => $this->locationRepositoryMock
        ]);
    }

    public function testExecuteWithoutLocationId()
    {
        $this->messageManagerMock->expects($this->atLeastOnce())
            ->method('addErrorMessage')
            ->with('We can\'t find a location to delete.');

        $this->subject->execute();
    }

    public function testExecuteWithLocationLoadException()
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('location_id')
            ->willReturn(1);

        $this->locationRepositoryMock->expects($this->atLeastOnce())
            ->method('getById')
            ->with(1)
            ->willThrowException(new NotFoundException(__('No such entity')));

        $this->messageManagerMock->expects($this->atLeastOnce())
            ->method('addErrorMessage')
            ->with('No such entity');

        $this->subject->execute();
    }

    public function testExecute()
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('location_id')
            ->willReturn(1);

        $locationMock = $this->createMock(LocationInterface::class);

        $this->locationRepositoryMock->expects($this->atLeastOnce())
            ->method('getById')
            ->with(1)
            ->willReturn($locationMock);

        $this->messageManagerMock->expects($this->atLeastOnce())
            ->method('addSuccessMessage')
            ->with('You deleted the location.');

        $this->subject->execute();
    }
}
