<?php

namespace AdeoWeb\Dpd\Test\Unit\Ui\Component\Listing\Column;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Ui\Component\Listing\Column\LocationActions;

class LocationActionsTest extends AbstractTest
{
    /**
     * @var LocationActions
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(LocationActions::class);
    }

    public function testPrepareDataSource()
    {
        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'location_id' => 1
                    ]
                ]
            ]
        ];

        $this->subject->setData('name', 'test');

        $result = $this->subject->prepareDataSource($dataSource);

        $this->assertArrayHasKey('edit', $result['data']['items'][0]['test']);
        $this->assertArrayHasKey('delete', $result['data']['items'][0]['test']);
    }
}
