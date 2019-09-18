<?php

namespace Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Controller\Adminhtml\Location\Index;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class IndexTest extends AbstractTest
{
    /**
     * @var Index
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $pageMock;

    public function setUp()
    {
        parent::setUp();

        $this->pageMock = $this->createMock(\Magento\Framework\View\Result\Page::class);

        $pageFactoryMock = $this->createMock(\Magento\Framework\View\Result\PageFactory::class);
        $pageFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->pageMock);

        $this->subject = $this->objectManager->getObject(Index::class, [
            'resultPageFactory' => $pageFactoryMock
        ]);
    }

    public function testExecute()
    {
        $titleMock = $this->createMock(\Magento\Framework\View\Page\Title::class);

        $configMock = $this->createMock(\Magento\Framework\View\Page\Config::class);
        $configMock->expects($this->atLeastOnce())
            ->method('getTitle')
            ->willReturn($titleMock);

        $this->pageMock->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn($configMock);

        $result = $this->subject->execute();
        $expectedResult = $this->pageMock;

        $this->assertEquals($expectedResult, $result);
    }
}
