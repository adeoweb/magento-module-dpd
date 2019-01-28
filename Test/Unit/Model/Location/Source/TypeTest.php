<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Location\Source;

use AdeoWeb\Dpd\Model\Location\Source\Type;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class TypeTest extends AbstractTest
{
    /**
     * @var Type
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(Type::class);
    }

    public function testToOptionArray()
    {
        $result = $this->subject->toOptionArray();
        $expectedResult = [
            [
                'label' => __('Warehouse'),
                'value' => 1,
            ],
            [
                'label' => __('Destination'),
                'value' => 2,
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
