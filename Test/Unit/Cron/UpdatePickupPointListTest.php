<?php

namespace AdeoWeb\Dpd\Test\Unit\Cron;

use AdeoWeb\Dpd\Cron\UpdatePickupPointList;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class UpdatePickupPointListTest extends AbstractTest
{
    /**
     * @var UpdatePickupPointList
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $pickupPointManagementMock;

    /**
     * @var MockObject
     */
    private $loggerMock;

    public function setUp()
    {
        parent::setUp();

        $this->pickupPointManagementMock = $this->createMock(\AdeoWeb\Dpd\Api\PickupPointManagementInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->subject = $this->objectManager->getObject(UpdatePickupPointList::class,
            [
                'pickupPointManagement' => $this->pickupPointManagementMock,
                'logger' => $this->loggerMock,
            ]
        );
    }

    public function testExecute()
    {
        $this->pickupPointManagementMock->method('update')
            ->willReturn(['US' => 'Some warning']);

        $this->loggerMock->expects($this->atLeastOnce())
            ->method('warning')
            ->with('<error>Error encountered while updating DPD pickup point list for "US": Some warning</error>');

        $this->subject->execute();
    }
}
