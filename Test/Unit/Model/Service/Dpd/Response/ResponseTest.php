<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Service\Dpd\Response;

use AdeoWeb\Dpd\Model\Service\Dpd\Response\Response;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class ResponseTest extends AbstractTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetBodyWithNonExistingKey()
    {
        $subject = $this->objectManager->getObject(Response::class, [
            'error' => false,
            'errorMessage' => '',
            'body' => [
                'testResult' => 12345
            ]
        ]);

        $result = $subject->getBody('test');

        $this->assertEmpty($result);
    }

    public function testGetBodyWithKey()
    {
        $subject = $this->objectManager->getObject(Response::class, [
            'error' => false,
            'errorMessage' => '',
            'body' => [
                'testResult' => 12345
            ]
        ]);

        $result = $subject->getBody('testResult');

        $this->assertEquals(12345, $result);
    }

    public function testGetBody()
    {
        $subject = $this->objectManager->getObject(Response::class, [
            'error' => false,
            'errorMessage' => '',
            'body' => [
                'testResult' => 12345
            ]
        ]);

        $result = $subject->getBody();

        $this->assertEquals(['testResult' => 12345], $result);
    }
}
