<?php

namespace AdeoWeb\Dpd\Test\Unit\Helper\Config;

use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class SerializerTest extends AbstractTest
{
    /**
     * @var MockObject|\Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serializerMock;

    /**
     * @var Serializer
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->serializerMock = $this->createMock(\Magento\Framework\Serialize\Serializer\Serialize::class);

        $this->subject = $this->objectManager->getObject(Serializer::class, ['serializer' => $this->serializerMock]);
    }

    public function testUnserialize()
    {
        $emptyString = '';
        $jsonString = '{"a":"b","c":{"d":"e","f":"g"}}';
        $serializedString = 'a:2:{s:1:"a";s:1:"b";s:1:"c";a:2:{s:1:"d";s:1:"e";s:1:"f";s:1:"g";}}';

        $this->serializerMock->expects($this->any())->method('unserialize')->will($this->returnValueMap([
            [$serializedString, ['a' => 'b', 'c' => ['d' => 'e', 'f' => 'g']]]
        ]));

        $this->assertEquals([], $this->subject->unserialize($emptyString));
        $this->assertEquals(
            ['a' => 'b', 'c' => ['d' => 'e', 'f' => 'g']],
            $this->subject->unserialize($jsonString)
        );
        $this->assertEquals(
            ['a' => 'b', 'c' => ['d' => 'e', 'f' => 'g']],
            $this->subject->unserialize($serializedString)
        );
    }
}
