<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Model\Location;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class LocationTest extends AbstractTest
{
    /**
     * @var Location
     */
    private $subject;

    /**
     * @var LocationInterface
     */
    private $location;

    public function setUp()
    {
        parent::setUp();

        $this->location = $this->objectManager->getObject(\AdeoWeb\Dpd\Model\Data\Location::class);

        $locationDataFactoryMock = $this->createMock(\AdeoWeb\Dpd\Api\Data\LocationInterfaceFactory::class);
        $locationDataFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->location);

        $joinProcessorMock = $this->createMock(\Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface::class);
        $joinProcessorMock->expects($this->any())
            ->method('extractExtensionAttributes')
            ->willReturn([
                'name' => 'TestName',
            ]);

        $dataObjectHelper = $this->objectManager->getObject(\Magento\Framework\Api\DataObjectHelper::class,
            [
                'joinProcessor' => $joinProcessorMock,
            ]);

        $this->subject = $this->objectManager->getObject(Location::class, [
            'locationDataFactory' => $locationDataFactoryMock,
            'dataObjectHelper' => $dataObjectHelper,
            'data' => [
                'name' => 'TestName',
            ],
        ]);
    }

    public function testGetDataModel()
    {
        $result = $this->subject->getDataModel();

        $this->assertInstanceOf(\AdeoWeb\Dpd\Api\Data\LocationInterface::class, $result);
        $this->assertEquals([
            'name' => 'TestName',
        ], $result->getData());
    }
}
