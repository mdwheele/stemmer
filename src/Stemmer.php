<?php

/**
 * Class Stemmer
 *
 * @see http://tartarus.org/~martin/PorterStemmer/def.txt
 * @todo Integrate document reference with code tightly.
 */
class Stemmer
{
    /**
     * Regex for finding consonants.
     *
     * This is a non-capturing regex (?:).  It will match strings that contain character(s) [b...z] OR
     * character [y] preceded by a vowel OR if the character [y] is the only character in the string.
     *
     * @var string
     */
    private static $consonant_regex = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';

    /**
     * Regex for finding vowels.
     *
     * This is a non-capturing regex (?:).  It will match strings that contain character(s) [aeiou] OR
     * character [y] is not preceded by a vowel.
     *
     * @var string
     */
    private static $vowel_regex = '(?:[aeiou]|(?<![aeiou])y)';

    /**
     * Produces the stem of a word.
     *
     * @param string $word Word to produce stem of.
     * @return string Stem of word.
     */
    public static function stem($word)
    {
        if(strlen($word <= 2)){
            return $word;
        }

        $word = self::step1a($word);
        $word = self::step1b($word);
        $word = self::step1c($word);
        $word = self::step2($word);
        $word = self::step3($word);
        $word = self::step4($word);
        $word = self::step5a($word);
        $word = self::step5b($word);
    }

    private static function step1a($word)
    {
        self::replace('sses', 'ss', $word) OR
        self::replace('ies', 'i', $word) OR
        self::replace('ies', 'i', $word) OR
        self::replace('ies', 'i', $word);

        return $word;
    }

    private static function step1b($word)
    {
        $second_or_third_successful = false;

        if(self::measure($word) > 0 AND self::stringEndsWith($word, 'eed')) {
            self::replace('eed', 'ee', $word);
        } elseif (self::stemContainsVowel(self::proposedStem($word, 3)) AND self::stringEndsWith($word, 'ing')) {
            self::replace('ing', '', $word);
        } elseif (self::stemContainsVowel(self::proposedStem($word, 2)) AND self::stringEndsWith($word, 'ed')) {
            self::replace('eed', 'ee', $word);
        }

        if($second_or_third_successful) {
            if( !self::replace('at', 'ate', $word) AND
                !self::replace('bl', 'ble', $word) AND
                !self::replace('iz', 'ize', $word)) {
                if(
                    self::stemEndsWithDoubleConsonant($word) AND
                    !(
                        self::stringEndsWith($word, 'l') OR
                        self::stringEndsWith($word, 's') OR
                        self::stringEndsWith($word, 'z')
                    )
                ) {
                    $word = substr($word, 0, -1);
                } elseif (self::measure($word) == 1 AND self::stemEndsWithCVC($word)) {
                    $word .= 'e';
                }
            }
        }

        return $word;
    }

    private static function step1c($word)
    {
        if(self::stemContainsVowel(self::proposedStem($word, 1))) {
            self::replace('y', 'i', $word);
        }

        return $word;
    }

    private static function step2($word)
    {
        if(self::measure($word) > 0) {
            self::replace('ational', 'ate', $word) OR 
            self::replace('tional', 'tion', $word) OR 
            self::replace('ization', 'ize', $word) OR 
            self::replace('iveness', 'ive', $word) OR 
            self::replace('fulness', 'ful', $word) OR 
            self::replace('ousness', 'ous', $word) OR 
            self::replace('biliti', 'ble', $word) OR 
            self::replace('entli', 'ent', $word) OR 
            self::replace('ousli', 'ous', $word) OR 
            self::replace('ation', 'ate', $word) OR 
            self::replace('alism', 'al', $word) OR 
            self::replace('aliti', 'al', $word) OR 
            self::replace('iviti', 'ive', $word) OR 
            self::replace('enci', 'ence', $word) OR 
            self::replace('anci', 'ance', $word) OR 
            self::replace('izer', 'ize', $word) OR 
            self::replace('abli', 'able', $word) OR 
            self::replace('ator', 'ate', $word) OR 
            self::replace('eli', 'e', $word);
        }

        return $word;
    }

    private static function step3($word)
    {
        if(self::measure($word) > 0) {
            self::replace('icate', 'ic', $word) OR 
            self::replace('ative', '', $word) OR 
            self::replace('alize', 'al', $word) OR 
            self::replace('iciti', 'ic', $word) OR 
            self::replace('ical', 'ic', $word) OR 
            self::replace('ful', '', $word) OR 
            self::replace('ness', '', $word);
        }

        return $word;
    }

    private static function step4($word)
    {
        if(self::measure($word) > 1) {
            self::replace('al', '', $word) OR 
            self::replace('ance', '', $word) OR 
            self::replace('ence', '', $word) OR 
            self::replace('er', '', $word) OR 
            self::replace('ic', '', $word) OR 
            self::replace('able', '', $word) OR 
            self::replace('ible', '', $word) OR 
            self::replace('ant', '', $word) OR 
            self::replace('ement', '', $word) OR 
            self::replace('ment', '', $word) OR 
            self::replace('ent', '', $word);

            if(self::stemEndsWithLetter($word, 's') OR self::stemEndsWithLetter($word, 't')) {
                self::replace('ion', '', $word);
            }
            else {
                self::replace('ou', '', $word) OR
                self::replace('ism', '', $word) OR
                self::replace('ate', '', $word) OR
                self::replace('iti', '', $word) OR
                self::replace('ous', '', $word) OR
                self::replace('ive', '', $word) OR
                self::replace('ize', '', $word);
            }
        }

        return $word;
    }

    private static function step5a($word)
    {
        if(self::measure($word) > 1) {
            self::replace('e', '', $word);
        } elseif (self::measure($word) == 1 AND !self::stemEndsWithCVC($word)) {
            self::replace('e', '', $word);
        }

        return $word;
    }

    private static function step5b($word)
    {
        if( self::measure($word) > 1 AND
            self::stemEndsWithDoubleConsonant($word) AND
            self::stemEndsWithLetter($word, 'l')
        ) {
            $word = substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * Returns the number of vowel-consonant sequences in a string; referred to as 'measure'.
     *
     * @see http://tartarus.org/~martin/PorterStemmer/def.txt
     *
     * A consonant will be denoted by c, a vowel by v. A list ccc... of length
     * greater than 0 will be denoted by C, and a list vvv... of length greater
     * than 0 will be denoted by V. Any word, or part of a word, therefore has one
     * of the four forms:
     *
     * CVCV ... C
     * CVCV ... V
     * VCVC ... C
     * VCVC ... V
     *
     * These may all be represented by the single form
     *
     * [C]VCVC ... [V]
     *
     * where the square brackets denote arbitrary presence of their contents.
     * Using (VC){m} to denote VC repeated m times, this may again be written as
     *
     * [C](VC){m}[V].
     *
     * m will be called the \measure\ of any word or word part when represented in
     * this form. The case m = 0 covers the null word. Here are some examples:
     *
     * m=0    TR,  EE,  TREE,  Y,  BY.
     * m=1    TROUBLE,  OATS,  TREES,  IVY.
     * m=2    TROUBLES,  PRIVATE,  OATEN,  ORRERY.
     *
     * @param $word
     * @return int number of vowel-consonant sequences.
     */
    private static function measure($word)
    {
        $c = self::$consonant_regex;
        $v = self::$vowel_regex;

        // Remove all consonants from the beginning of the word and all vowels from the end.
        $word = preg_replace("#^$c+#", "", $word);
        $word = preg_replace("#$v+#", "", $word);

        // Find all matches of a sequence of vowels followed by a sequence of consonants.
        preg_match_all("#($v+$c+)#", $word, $matches);

        // Return count of matches.  Since the above is grouped, the entire match is in [0], the individual
        // matches are in [1].
        return count($matches[1]);
    }

    private static function replace($search, $replace, &$subject)
    {
        $foundSearchStringAtEnd = preg_match("#$search$#i", $subject);

        $subject = preg_replace("#$search$#i", $replace, $subject);

        return $foundSearchStringAtEnd;
    }

    /**
     * Returns a proposed stem for doing additional computation on.  It's basically a domain-specific
     * wrapper around culling the last $suffix_length characters off a string.
     *
     * @param $string full string
     * @param $suffix_length number of letters to cull off the end
     * @return string the resultant 'stem' of the operation.
     */
    private static function proposedStem($string, $suffix_length)
    {
        return substr($string, 0, -$suffix_length);
    }

    private static function stringEndsWith($string, $search)
    {
        return preg_match("#$search$#i", $string);
    }

    /**
     * Returns true or false as to whether the stem ends with letter.
     *
     * @param string $stem the stem
     * @param string $letter the letter to assert whether it is at end of stem
     * @return bool whether or not the stem ends with the letter provided.
     */
    private static function stemEndsWithLetter($stem, $letter)
    {
        return substr($stem, -1) == $letter;
    }

    /**
     * Returns true or false as to whether the stem contains a vowel.
     *
     * @param string $stem the proposed stem to test against.
     * @return bool whether or not the proposed stem contains a vowel.
     */
    private static function stemContainsVowel($stem)
    {
        $v = self::$vowel_regex;
        return preg_match("#$v+#", $stem);
    }

    /**
     * Returns true or false as to whether the string contains two consonants
     * that are equal and next to each other at the end of the stem.
     *
     * @param string $stem the proposed stem to test against.
     * @return bool
     */
    private static function stemEndsWithDoubleConsonant($stem)
    {
        $c = self::$consonant_regex;
        preg_match("#$c{2}$#", $stem, $matches);

        $first_character = $matches[0][0];
        $second_character = $matches[0][1];

        return $first_character == $second_character;
    }

    /**
     * Returns true or false as to whether the stem ends with a consonant-vowel-consonant
     * sequence AND the second consonant is not [w], [x], or [y].
     *
     * @param string $stem the proposed stem to test against.
     * @return bool
     */
    private static function stemEndsWithCVC($stem)
    {
        $v = self::$vowel_regex;
        $c = self::$consonant_regex;

        preg_match("#($c$v$c)$#", $stem, $matches);
        $last_letter = $matches[1][2];

        return strlen($matches[1]) == 3 AND !in_array($last_letter, array('x','y','z'));
    }
}