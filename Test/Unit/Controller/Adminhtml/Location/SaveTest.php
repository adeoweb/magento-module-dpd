<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Model\Location;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Controller\Adminhtml\Location\Save;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class SaveTest extends AbstractTest
{
    /**
     * @var Save
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $requestMock;

    /**
     * @var MockObject
     */
    private $resultRedirectMock;

    /**
     * @var MockObject
     */
    private $locationRepositoryMock;

    /**
     * @var MockObject
     */
    private $messageManagerMock;

    /**
     * @var MockObject
     */
    private $dataPersistorMock;

    public function setUp()
    {
        parent::setUp();

        $this->requestMock = $this->createMock(Http::class);
        $this->resultRedirectMock = $this->createMock(\Magento\Backend\Model\View\Result\Redirect::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);

        $resultRedirectFactoryMock = $this->createMock(\Magento\Backend\Model\View\Result\RedirectFactory::class);
        $resultRedirectFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultRedirectMock);


        $contextMock = $this->objectManager->getObject(\Magento\Backend\App\Action\Context::class, [
            'request' => $this->requestMock,
            'resultRedirectFactory' => $resultRedirectFactoryMock,
            'messageManager' => $this->messageManagerMock
        ]);

        $this->locationRepositoryMock = $this->createMock(LocationRepositoryInterface::class);
        $this->dataPersistorMock = $this->createMock(\AdeoWeb\Dpd\Model\App\Request\DataPersistorInterface::class);

        $this->subject = $this->objectManager->getObject(Save::class, [
            'context' => $contextMock,
            'locationRepository' => $this->locationRepositoryMock,
            'dataPersistor' => $this->dataPersistorMock
        ]);
    }

    public function testExecuteWithEmptyPostData()
    {
        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getPostValue')
            ->willReturn([]);

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturn($this->resultRedirectMock);

        $result = $this->subject->execute();
        $expectedResult = $this->resultRedirectMock;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteWithLocalizedException()
    {
        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getPostValue')
            ->willReturn(['test']);

        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getParam')
            ->with('location_id')
            ->willReturn(1);

        $this->locationRepositoryMock
            ->expects(self::atLeastOnce())
            ->method('getById')
            ->willThrowException(new LocalizedException(__('Invalid entity id')));

        $this->messageManagerMock
            ->expects(self::atLeastOnce())
            ->method('addErrorMessage')
            ->with('Invalid entity id');

        $this->dataPersistorMock
            ->expects(self::atLeastOnce())
            ->method('set')
            ->with('adeoweb_dpd_location', ['test']);

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/edit', ['location_id' => 1])
            ->willReturn($this->resultRedirectMock);

        $result = $this->subject->execute();
        $expectedResult = $this->resultRedirectMock;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteWithUnspecifiedException()
    {
        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getPostValue')
            ->willReturn(['test']);

        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getParam')
            ->with('location_id')
            ->willReturn(1);

        $expectedException = new \Exception('Invalid data');

        $this->locationRepositoryMock
            ->expects(self::atLeastOnce())
            ->method('getById')
            ->willThrowException($expectedException);

        $this->messageManagerMock
            ->expects(self::atLeastOnce())
            ->method('addExceptionMessage')
            ->with($expectedException, __('Something went wrong while saving the location.'));

        $this->dataPersistorMock
            ->expects(self::atLeastOnce())
            ->method('set')
            ->with('adeoweb_dpd_location', ['test']);

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/edit', ['location_id' => 1])
            ->willReturn($this->resultRedirectMock);

        $result = $this->subject->execute();
        $expectedResult = $this->resultRedirectMock;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteWithBackParameter()
    {
        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getPostValue')
            ->willReturn(['test']);

        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getParam')
            ->withConsecutive(['location_id'], ['back'])
            ->willReturnOnConsecutiveCalls(1, true);

        $locationMock = $this->createMock(Location::class);
        $locationMock
            ->expects(self::atLeastOnce())
            ->method('__call')
            ->with('getLocationId')
            ->willReturn(1);

        $this->locationRepositoryMock
            ->expects(self::atLeastOnce())
            ->method('getById')
            ->willReturn($locationMock);

        $this->messageManagerMock
            ->expects(self::once())
            ->method('addSuccessMessage')
            ->with(__('You saved the location.'));

        $this->dataPersistorMock
            ->expects(self::once())
            ->method('clear')
            ->with('adeoweb_dpd_location');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/edit', ['location_id' => 1])
            ->willReturn($this->resultRedirectMock);

        $result = $this->subject->execute();
        $expectedResult = $this->resultRedirectMock;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecute()
    {
        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getPostValue')
            ->willReturn(['test']);

        $this->requestMock
            ->expects(self::atLeastOnce())
            ->method('getParam')
            ->withConsecutive(['location_id'], ['back'])
            ->willReturnOnConsecutiveCalls(1, false);

        $locationMock = $this->createMock(Location::class);

        $this->locationRepositoryMock
            ->expects(self::atLeastOnce())
            ->method('getById')
            ->willReturn($locationMock);

        $this->messageManagerMock
            ->expects(self::once())
            ->method('addSuccessMessage')
            ->with(__('You saved the location.'));

        $this->dataPersistorMock
            ->expects(self::once())
            ->method('clear')
            ->with('adeoweb_dpd_location');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturn($this->resultRedirectMock);

        $result = $this->subject->execute();
        $expectedResult = $this->resultRedirectMock;

        $this->assertEquals($expectedResult, $result);
    }
}