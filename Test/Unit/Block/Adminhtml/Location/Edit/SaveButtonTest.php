<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Location\Edit;

use AdeoWeb\Dpd\Block\Adminhtml\Location\Edit\SaveButton;
use PHPUnit\Framework\MockObject\MockObject;

class SaveButtonTest extends \AdeoWeb\Dpd\Test\Unit\AbstractTest
{
    /**
     * @var SaveButton
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(SaveButton::class);
    }

    public function testGetButtonData()
    {
        $result = $this->subject->getButtonData();
        $expectedResult = [
            'label' => __('Save Location'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];

        $this->assertEquals($result, $expectedResult);
    }
}
