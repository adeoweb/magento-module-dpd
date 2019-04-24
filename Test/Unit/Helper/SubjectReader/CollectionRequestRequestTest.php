<?php

namespace AdeoWeb\Dpd\Helper\SubjectReader;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class CollectionRequestRequestTest extends AbstractTest
{
    /**
     * @var CollectionRequestRequest
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(CollectionRequestRequest::class);
    }

    public function testReadOrderIdWithOptionalParam()
    {
        $subject = [];

        $this->assertNull($this->subject->readOrderId($subject));
    }

    public function testReadTotalWeightConvertedToFloat()
    {
        $subject = [
            'total_weight' => 10,
        ];

        $this->assertSame(10.0, $this->subject->readTotalWeight($subject));
    }

    public function testReadNumOfParcelsConvertedToInt()
    {
        $subject = [
            'num_of_parcels' => '10',
        ];

        $this->assertSame(10, $this->subject->readNumOfParcels($subject));
    }
}
