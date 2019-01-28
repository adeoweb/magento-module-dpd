<?php

namespace AdeoWeb\Dpd\Test\Unit\Helper\SubjectReader;

use AdeoWeb\Dpd\Helper\SubjectReader\AbstractSubjectReader;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Exception\LocalizedException;

class AbstractSubjectReaderTest extends AbstractTest
{
    /**
     * @var AbstractSubjectReader
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->createPartialMock(AbstractSubjectReader::class, []);
    }

    public function testRead()
    {
        $subject1 = [
            'a' => 'b'
        ];
        $subject2 = [
            'a' => [
                'b' => 'c'
            ]
        ];
        $subject3 = [
            'a' => [
                'b' => 'c'
            ]
        ];

        $this->assertEquals('b', $this->subject->read('a', null, $subject1));
        $this->assertEquals('c', $this->subject->read('b', 'a', $subject2));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Parameter "e" is missing from subject');

        return $this->subject->read('e', 'a', $subject3);
    }
}
