<?php


class StemmerTest extends PHPUnit_Framework_TestCase
{
    public function testStep1a()
    {
        $this->assertEquals('caress', Stemmer::step1a('caresses'));
        $this->assertEquals('poni', Stemmer::step1a('ponies'));
        $this->assertEquals('ti', Stemmer::step1a('ties'));
        $this->assertEquals('caress', Stemmer::step1a('caress'));
        $this->assertEquals('cat', Stemmer::step1a('cats'));
    }

    public function testStep1b()
    {
        $this->assertEquals('feed', Stemmer::step1b('feed'));
        $this->assertEquals('agree', Stemmer::step1b('agreed'));
        $this->assertEquals('plaster', Stemmer::step1b('platered'));
        $this->assertEquals('bled', Stemmer::step1b('bled'));
        $this->assertEquals('motor', Stemmer::step1b('motoring'));
        $this->assertEquals('sing', Stemmer::step1b('sing'));

        $this->assertEquals('conflate', Stemmer::step1b('conflated'));
        $this->assertEquals('trouble', Stemmer::step1b('troubled'));
        $this->assertEquals('size', Stemmer::step1b('sized'));
        $this->assertEquals('hop', Stemmer::step1b('hopping'));
        $this->assertEquals('tan', Stemmer::step1b('tanned'));
        $this->assertEquals('fall', Stemmer::step1b('falling'));
        $this->assertEquals('hiss', Stemmer::step1b('hissing'));
        $this->assertEquals('fizz', Stemmer::step1b('fizzed'));
        $this->assertEquals('fail', Stemmer::step1b('failing'));
        $this->assertEquals('file', Stemmer::step1b('filing'));
    }
}