<?php

namespace Model\Service\Dpd;

use AdeoWeb\Dpd\Model\Service\Dpd\ResponseFactory;
use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class ResponseFactoryTest extends AbstractTest
{
    /**
     * @var ResponseFactory
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $objectManagerMock;

    public function setUp()
    {
        parent::setUp();

        $this->objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);

        $this->subject = $this->objectManager->getObject(ResponseFactory::class, [
            'objectManager' => $this->objectManagerMock
        ]);
    }

    public function testCreateWithException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid response from DPD service');

        return $this->subject->create([]);
    }

    public function testCreate()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('AdeoWeb\Dpd\Model\Service\Dpd\Response\Response', ['error' => true, 'errorMessage' => 'Something went wrong', 'body' => ['testResult' => 123]])
            ->willReturn($this->createMock(ResponseInterface::class));

        $result = $this->subject->create(['status' => 'err', 'errlog' => 'Something went wrong', 'testResult' => 123]);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
