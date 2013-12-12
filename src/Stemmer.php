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

        return $word;
    }

    public static function step1a($word)
    {
        self::replace('sses', 'ss', $word) OR
        self::replace('ies', 'i', $word) OR
        self::replace('ss', 'ss', $word) OR
        self::replace('s', '', $word);

        return $word;
    }

    public static function step1b($word)
    {
        $second_or_third_successful = false;

        if(!self::replace('eed', 'ee', $word, 0)) {
            if (self::stringContainsVowel(self::proposedString($word, 3)) AND self::stringEndsWith($word, 'ing')) {
                self::replace('ing', '', $word);
                $second_or_third_successful = true;
            } elseif (self::stringContainsVowel(self::proposedString($word, 2)) AND self::stringEndsWith($word, 'ed')) {
                self::replace('ed', '', $word);
                $second_or_third_successful = true;
            }
        }

        if($second_or_third_successful) {
            if( !self::replace('at', 'ate', $word) AND
                !self::replace('bl', 'ble', $word) AND
                !self::replace('iz', 'ize', $word)) {
                if(
                    self::stringEndsWithDoubleConsonant($word) AND
                    !(
                        self::stringEndsWith($word, 'l') OR
                        self::stringEndsWith($word, 's') OR
                        self::stringEndsWith($word, 'z')
                    )
                ) {
                    $word = substr($word, 0, -1);
                } elseif (self::measure($word) == 1 AND self::stringEndsWithCVC($word)) {
                    $word .= 'e';
                }
            }
        }

        return $word;
    }

    public static function step1c($word)
    {
        if(self::stringContainsVowel(self::proposedString($word, 1))) {
            self::replace('y', 'i', $word);
        }

        return $word;
    }

    public static function step2($word)
    {
        self::replace('ational', 'ate', $word, 0) OR 
        self::replace('tional', 'tion', $word, 0) OR 
        self::replace('ization', 'ize', $word, 0) OR 
        self::replace('iveness', 'ive', $word, 0) OR 
        self::replace('fulness', 'ful', $word, 0) OR 
        self::replace('ousness', 'ous', $word, 0) OR 
        self::replace('biliti', 'ble', $word, 0) OR 
        self::replace('entli', 'ent', $word, 0) OR 
        self::replace('ousli', 'ous', $word, 0) OR 
        self::replace('ation', 'ate', $word, 0) OR 
        self::replace('alism', 'al', $word, 0) OR 
        self::replace('aliti', 'al', $word, 0) OR 
        self::replace('iviti', 'ive', $word, 0) OR 
        self::replace('enci', 'ence', $word, 0) OR 
        self::replace('anci', 'ance', $word, 0) OR 
        self::replace('izer', 'ize', $word, 0) OR
        self::replace('abli', 'able', $word, 0) OR
        self::replace('alli', 'al', $word, 0) OR
        self::replace('ator', 'ate', $word, 0) OR 
        self::replace('eli', 'e', $word, 0);

        return $word;
    }

    public static function step3($word)
    {
        self::replace('icate', 'ic', $word, 0) OR
        self::replace('ative', '', $word, 0) OR
        self::replace('alize', 'al', $word, 0) OR
        self::replace('iciti', 'ic', $word, 0) OR
        self::replace('ical', 'ic', $word, 0) OR
        self::replace('ful', '', $word, 0) OR
        self::replace('ness', '', $word, 0);

        return $word;
    }

    public static function step4($word)
    {
        self::replace('al', '', $word, 1) OR
        self::replace('ance', '', $word, 1) OR
        self::replace('ence', '', $word, 1) OR
        self::replace('er', '', $word, 1) OR
        self::replace('ic', '', $word, 1) OR
        self::replace('able', '', $word, 1) OR
        self::replace('ible', '', $word, 1) OR
        self::replace('ant', '', $word, 1) OR
        self::replace('ement', '', $word, 1) OR
        self::replace('ment', '', $word, 1) OR
        self::replace('ent', '', $word, 1);

        if(!((self::stringEndsWithLetter(self::proposedString($word, 3), 's') OR self::stringEndsWithLetter(self::proposedString($word, 3), 't')) AND self::replace('ion', '', $word, 1))) {
            self::replace('ou', '', $word, 1) OR
            self::replace('ism', '', $word, 1) OR
            self::replace('ate', '', $word, 1) OR
            self::replace('iti', '', $word, 1) OR
            self::replace('ous', '', $word, 1) OR
            self::replace('ive', '', $word, 1) OR
            self::replace('ize', '', $word, 1);
        }

        return $word;
    }

    public static function step5a($word)
    {
        if(self::measure(self::proposedString($word, 1)) > 1) {
            self::replace('e', '', $word, 1);
        } elseif (self::measure(self::proposedString($word, 1)) == 1 AND !self::stringEndsWithCVC(self::proposedString($word, 1))) {
            self::replace('e', '', $word);
        }

        return $word;
    }

    public static function step5b($word)
    {
        if( self::measure($word) > 1 AND
            self::stringEndsWithDoubleConsonant($word) AND
            self::stringEndsWithLetter($word, 'l')
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
    public static function measure($word)
    {
        $c = self::$consonant_regex;
        $v = self::$vowel_regex;

        // Remove all consonants from the beginning of the word and all vowels from the end.
        $word = preg_replace("#^$c+#", "", $word);
        $word = preg_replace("#$v+$#", "", $word);

        // Find all matches of a sequence of vowels followed by a sequence of consonants.
        preg_match_all("#($v+$c+)#", $word, $matches);

        // Return count of matches.  Since the above is grouped, the entire match is in [0], the individual
        // matches are in [1].
        return count($matches[1]);
    }

    /**
     * Replace search string if the result has a measure greater than what is set OR is null.
     *
     * @param $search string to search for
     * @param $replace string to replace search term if found
     * @param $subject string to search
     * @param null $m optional minimum resultant measure that must be met
     * @return boolean whether or not the search string was found at the end of the original string
     */
    public static function replace($search, $replace, &$subject, $m = null)
    {
        $foundSearchStringAtEnd = preg_match("#$search$#i", $subject);

        $replaced = preg_replace("#$search$#i", $replace, $subject);

        if(is_null($m) OR self::measure(substr($subject, 0, -strlen($search))) > $m) {
            $subject = $replaced;
        }

        return $foundSearchStringAtEnd;
    }

    /**
     * Returns a proposed string for doing additional computation on.  It's basically a domain-specific
     * wrapper around culling the last $suffix_length characters off a string.
     *
     * @param $string full string
     * @param $suffix_length number of letters to cull off the end
     * @return string the resultant 'string' of the operation.
     */
    public static function proposedString($string, $suffix_length)
    {
        return substr($string, 0, -$suffix_length);
    }

    public static function stringEndsWith($string, $search)
    {
        return preg_match("#$search$#i", $string);
    }

    /**
     * Returns true or false as to whether the string ends with letter.
     *
     * @param string $string the string
     * @param string $letter the letter to assert whether it is at end of string
     * @return bool whether or not the string ends with the letter provided.
     */
    public static function stringEndsWithLetter($string, $letter)
    {
        return substr($string, -1) == $letter;
    }

    /**
     * Returns true or false as to whether the string contains a vowel.
     *
     * @param string $string the proposed string to test against.
     * @return bool whether or not the proposed string contains a vowel.
     */
    public static function stringContainsVowel($string)
    {
        $v = self::$vowel_regex;
        return preg_match("#$v+#", $string);
    }

    /**
     * Returns true or false as to whether the string contains two consonants
     * that are equal and next to each other at the end of the string.
     *
     * @param string $string the proposed string to test against.
     * @return bool
     */
    public static function stringEndsWithDoubleConsonant($string)
    {
        $c = self::$consonant_regex;
        preg_match("#$c{2}$#", $string, $matches);

        if(count($matches)) {
            $first_character = $matches[0][0];
            $second_character = $matches[0][1];
            return $first_character == $second_character;
        }

        return false;
    }

    /**
     * Returns true or false as to whether the string ends with a consonant-vowel-consonant
     * sequence AND the second consonant is not [w], [x], or [y].
     *
     * @param string $string the proposed string to test against.
     * @return bool
     */
    public static function stringEndsWithCVC($string)
    {
        $v = self::$vowel_regex;
        $c = self::$consonant_regex;

        preg_match("#($c$v$c)$#", $string, $matches);

        if(count($matches)) {
            $last_letter = $matches[1][2];
            return strlen($matches[1]) == 3 AND !in_array($last_letter, array('x','y','z'));
        }

        return false;
    }
}