<?php

namespace AdeoWeb\Dpd\Helper\SubjectReader;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class CallCourierRequestTest extends AbstractTest
{
    /**
     * @var CallCourierRequest
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(CallCourierRequest::class);
    }

    public function testReadTotalWeightConvertedToFloat()
    {
        $subject = [
            'total_weight' => 10
        ];

        $this->assertSame(10.0, $this->subject->readTotalWeight($subject));
    }

    public function testReadNumOfParcelsConvertedToInt()
    {
        $subject = [
            'num_of_parcels' => '10'
        ];

        $this->assertSame(10, $this->subject->readNumOfParcels($subject));
    }
}
