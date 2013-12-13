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
        $this->assertEquals('plaster', Stemmer::step1b('plastered'));
        $this->assertEquals('bled', Stemmer::step1b('bled'));
        $this->assertEquals('motor', Stemmer::step1b('motoring'));
        $this->assertEquals('sing', Stemmer::step1b('sing'));
        $this->assertEquals('abandon', Stemmer::step1b('abandoned'));

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

    public function testStep1c()
    {
        $this->assertEquals('happi', Stemmer::step1c('happy'));
        $this->assertEquals('sky', Stemmer::step1c('sky'));
    }

    public function testStep2()
    {
        $this->assertEquals('relate', Stemmer::step2('relational'));
        $this->assertEquals('condition', Stemmer::step2('conditional'));
        $this->assertEquals('rational', Stemmer::step2('rational'));
        $this->assertEquals('valence', Stemmer::step2('valenci'));
        $this->assertEquals('hesitance', Stemmer::step2('hesitanci'));
        $this->assertEquals('digitize', Stemmer::step2('digitizer'));
        $this->assertEquals('conformable', Stemmer::step2('conformabli'));
        $this->assertEquals('radical', Stemmer::step2('radicalli'));
        $this->assertEquals('different', Stemmer::step2('differentli'));
        $this->assertEquals('vile', Stemmer::step2('vileli'));
        $this->assertEquals('analogous', Stemmer::step2('analogousli'));
        $this->assertEquals('vietnamize', Stemmer::step2('vietnamization'));
        $this->assertEquals('predicate', Stemmer::step2('predication'));
        $this->assertEquals('operate', Stemmer::step2('operator'));
        $this->assertEquals('feudal', Stemmer::step2('feudalism'));
        $this->assertEquals('decisive', Stemmer::step2('decisiveness'));
        $this->assertEquals('hopeful', Stemmer::step2('hopefulness'));
        $this->assertEquals('callous', Stemmer::step2('callousness'));
        $this->assertEquals('formal', Stemmer::step2('formaliti'));
        $this->assertEquals('sensitive', Stemmer::step2('sensitiviti'));
        $this->assertEquals('sensible', Stemmer::step2('sensibiliti'));
    }

    public function testStep3()
    {
        $this->assertEquals('triplic', Stemmer::step3('triplicate'));
        $this->assertEquals('form', Stemmer::step3('formative'));
        $this->assertEquals('formal', Stemmer::step3('formalize'));
        $this->assertEquals('electric', Stemmer::step3('electriciti'));
        $this->assertEquals('electric', Stemmer::step3('electrical'));
        $this->assertEquals('hope', Stemmer::step3('hopeful'));
        $this->assertEquals('good', Stemmer::step3('goodness'));
    }
    
    public function testStep4()
    {
        $this->assertEquals('reviv', Stemmer::step4('revival'));
        $this->assertEquals('allow', Stemmer::step4('allowance'));
        $this->assertEquals('infer', Stemmer::step4('inference'));
        $this->assertEquals('airlin', Stemmer::step4('airliner'));
        $this->assertEquals('gyroscop', Stemmer::step4('gyroscopic'));
        $this->assertEquals('adjust', Stemmer::step4('adjustable'));
        $this->assertEquals('defens', Stemmer::step4('defensible'));
        $this->assertEquals('irrit', Stemmer::step4('irritant'));
        $this->assertEquals('replac', Stemmer::step4('replacement'));
        $this->assertEquals('adjust', Stemmer::step4('adjustment'));
        $this->assertEquals('depend', Stemmer::step4('dependent'));
        $this->assertEquals('adopt', Stemmer::step4('adoption'));
        $this->assertEquals('homolog', Stemmer::step4('homologou'));
        $this->assertEquals('commun', Stemmer::step4('communism'));
        $this->assertEquals('activ', Stemmer::step4('activate'));
        $this->assertEquals('angular', Stemmer::step4('angulariti'));
        $this->assertEquals('homolog', Stemmer::step4('homologous'));
        $this->assertEquals('effect', Stemmer::step4('effective'));
        $this->assertEquals('bowdler', Stemmer::step4('bowdlerize'));
    }
    
    public function testStep5a()
    {
        $this->assertEquals('probat', Stemmer::step5a('probate'));
        $this->assertEquals('rate', Stemmer::step5a('rate'));
        $this->assertEquals('ceas', Stemmer::step5a('cease'));
    }
    
    public function testStep5b()
    {
        $this->assertEquals('control', Stemmer::step5b('controll'));
        $this->assertEquals('roll', Stemmer::step5b('roll'));
    }

    public function testOneOffs()
    {
    }

    public function testAgainstDictionary()
    {
        return;

        $data = file("tests/data.txt", FILE_IGNORE_NEW_LINES);

        for($i=0; $i < count($data); $i++) {
            $line = preg_split('#\s+#', $data[$i]);

            $this->assertEquals($line[1], Stemmer::stem($line[0]));
        }
    }
}