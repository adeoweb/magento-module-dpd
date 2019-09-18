<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\ResourceModel;

use AdeoWeb\Dpd\Model\ResourceModel\Location;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class LocationTest extends AbstractTest
{
    /**
     * @var Location
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(Location::class);
    }

    public function testBeforeSave()
    {
        $location = $this->objectManager->getObject(\AdeoWeb\Dpd\Model\Location::class, []);

        $this->subject->_beforeSave($location);

        $result = $location->getData();

        $this->assertArrayHasKey('updated_at', $result);
        $this->assertArrayHasKey('created_at', $result);
    }
}
