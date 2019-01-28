<?php

namespace AdeoWeb\Dpd\Test\Unit\Helper\Config;

use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class SerializerTest extends AbstractTest
{
    /**
     * @var Serializer
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(Serializer::class);
    }

    public function testUnserialize()
    {
        $emptyString = '';
        $jsonString = '{"a":"b","c":{"d":"e","f":"g"}}';
        $serializedString = 'a:2:{s:1:"a";s:1:"b";s:1:"c";a:2:{s:1:"d";s:1:"e";s:1:"f";s:1:"g";}}';

        $this->assertEquals([], Serializer::unserialize($emptyString));
        $this->assertEquals(
            ['a' => 'b', 'c' => ['d' => 'e', 'f'=> 'g']],
            Serializer::unserialize($jsonString)
        );
        $this->assertEquals(
            ['a' => 'b', 'c' => ['d' => 'e', 'f'=> 'g']],
            Serializer::unserialize($serializedString)
        );
    }
}