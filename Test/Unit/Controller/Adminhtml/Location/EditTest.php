<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Controller\Adminhtml\Location\Edit;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class EditTest extends AbstractTest
{
    /**
     * @var Edit
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

    /**
     * @var MockObject
     */
    private $pageMock;

    /**
     * @var MockObject
     */
    private $locationMock;

    public function setUp()
    {
        parent::setUp();

        $this->requestMock = $this->createMock(Http::class);
        $this->responseMock = $this->createMock(\Magento\Framework\App\Response\Http::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);
        $this->locationMock = $this->createMock(LocationInterface::class);

        $this->redirectMock = $this->createMock(\Magento\Backend\Model\View\Result\Redirect::class);
        $this->pageMock = $this->createMock(\Magento\Backend\Model\View\Result\Page::class);

        $redirectFactoryMock = $this->createMock(\Magento\Backend\Model\View\Result\RedirectFactory::class);
        $redirectFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->redirectMock);

        $pageFactoryMock = $this->createMock(\Magento\Framework\View\Result\PageFactory::class);
        $pageFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->pageMock);

        $locationFactoryMock = $this->createConfiguredMock(\AdeoWeb\Dpd\Api\Data\LocationInterfaceFactory::class, [
            'create' => $this->locationMock
        ]);

        $contextMock = $this->objectManager->getObject(\Magento\Backend\App\Action\Context::class, [
            'request' => $this->requestMock,
            'response' => $this->responseMock,
            'resultRedirectFactory' => $redirectFactoryMock,
            'messageManager' => $this->messageManagerMock
        ]);

        $this->locationRepositoryMock = $this->createMock(\AdeoWeb\Dpd\Api\LocationRepositoryInterface::class);

        $this->subject = $this->objectManager->getObject(Edit::class, [
            'context' => $contextMock,
            'resultPageFactory' => $pageFactoryMock,
            'locationRepository' => $this->locationRepositoryMock,
            'locationFactory' => $locationFactoryMock
        ]);
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

    public function testExecuteWithNewItem()
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('location_id')
            ->willReturn(null);


        $titleMock = $this->createMock(\Magento\Framework\View\Page\Title::class);

        $pageConfigMock = $this->createMock(\Magento\Framework\View\Page\Config::class);
        $pageConfigMock->expects($this->atLeastOnce())
            ->method('getTitle')
            ->willReturn($titleMock);

        $this->pageMock->expects($this->atLeastOnce())
            ->method('setActiveMenu')
            ->willReturn($this->pageMock);
        $this->pageMock->expects($this->atLeastOnce())
            ->method('addBreadcrumb')
            ->willReturn($this->pageMock);
        $this->pageMock->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn($pageConfigMock);

        $result = $this->subject->execute();
        $expectedResult = $this->pageMock;

        $this->assertEquals($expectedResult, $result);
    }
}
