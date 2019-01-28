<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Action;

use AdeoWeb\Dpd\Controller\Adminhtml\Action\CloseManifest;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class CloseManifestTest extends AbstractTest
{
    /**
     * @var CloseManifest
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $closeManifestManagementMock;

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
    private $fileFactoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->closeManifestManagementMock = $this->createMock(\AdeoWeb\Dpd\Api\CloseManifestManagementInterface::class);
        $this->messageManagerMock = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);
        $this->responseMock = $this->createMock(\Magento\Framework\App\Response\Http::class);

        $contextMock = $this->objectManager->getObject(\Magento\Backend\App\Action\Context::class, [
            'response' => $this->responseMock,
            'messageManager' => $this->messageManagerMock
        ]);

        $this->fileFactoryMock = $this->createMock(\Magento\Framework\App\Response\Http\FileFactory::class);


        $this->subject = $this->objectManager->getObject(CloseManifest::class, [
            'closeManifestManagement' => $this->closeManifestManagementMock,
            'context' => $contextMock,
            'fileFactory' => $this->fileFactoryMock
        ]);
    }

    public function testExecuteWithCloseManifestError()
    {
        $this->closeManifestManagementMock->expects($this->atleastOnce())
            ->method('closeManifest')
            ->willThrowException(new \Exception('Invalid request'));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with('Invalid request');

        $this->subject->execute();
    }

    public function testExecute()
    {
        $this->closeManifestManagementMock->expects($this->atleastOnce())
            ->method('closeManifest')
            ->willReturn([
                base64_decode('JVBERi0xLjcKCjEgMCBvYmogICUgZW50cnkgcG9pbnQKPDwKICAvVHlwZSAvQ2F0YWxvZwogIC9QYWdlcyAyIDAgUgo+PgplbmRvYm' .
                'oKCjIgMCBvYmoKPDwKICAvVHlwZSAvUGFnZXMKICAvTWVkaWFCb3ggWyAwIDAgMjAwIDIwMCBdCiAgL0NvdW50IDEKICAvS2lkcyB' .
                'bIDMgMCBSIF0KPj4KZW5kb2JqCgozIDAgb2JqCjw8CiAgL1R5cGUgL1BhZ2UKICAvUGFyZW50IDIgMCBSCiAgL1Jlc291cmNlcyA8PA' .
                'ogICAgL0ZvbnQgPDwKICAgICAgL0YxIDQgMCBSIAogICAgPj4KICA+PgogIC9Db250ZW50cyA1IDAgUgo+PgplbmRvYmoKCjQgMCBvY' .
                'moKPDwKICAvVHlwZSAvRm9udAogIC9TdWJ0eXBlIC9UeXBlMQogIC9CYXNlRm9udCAvVGltZXMtUm9tYW4KPj4KZW5kb2JqCgo1IDA' .
                'gb2JqICAlIHBhZ2UgY29udGVudAo8PAogIC9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCjcwIDUwIFRECi9GMSAxMiBUZgooSGVsbG8sI'.
                'HdvcmxkISkgVGoKRVQKZW5kc3RyZWFtCmVuZG9iagoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDEwIDAwMDAw' .
                'IG4gCjAwMDAwMDAwNzkgMDAwMDAgbiAKMDAwMDAwMDE3MyAwMDAwMCBuIAowMDAwMDAwMzAxIDAwMDAwIG4gCjAwMDAwMDAzODAgMDAwM' .
                'DAgbiAKdHJhaWxlcgo8PAogIC9TaXplIDYKICAvUm9vdCAxIDAgUgo+PgpzdGFydHhyZWYKNDkyCiUlRU9G')
            ]);

        $this->subject->execute();
    }

}