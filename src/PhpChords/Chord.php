<?php

namespace PhpChords;

use Exception;

class Chord
{
    # Order matters!
    const NOTES_REGEX = [
        "/^(Do|do|DO|dO|c|C)#/" => 1,
        "/^(Do|do|DO|dO|c|C)/" => 0,
        "/^(Re|re|RE|rE|d|D)#/" => 3,
        "/^(Re|re|RE|rE|d|D)/" => 2,
        "/^(Mi|mi|MI|mI|e|E)/" => 4,
        "/^(Fa|fa|Fa|fA|f|F)#/" => 6,
        "/^(Fa|fa|Fa|fA|f|F)/" => 5,
        "/^(Sol|sol|SOL|sOL|soL|SoL|g|G)#/" => 8,
        "/^(Sol|sol|SOL|sOL|soL|SoL|g|G)/" => 7,
        "/^(La|la|LA|lA|a|A)#/" => 10,
        "/^(La|la|LA|lA|a|A)/" => 9,
        "/^(Si|si|SI|sI|b|B)/" => 11
    ];

    const NOTES_SPANISH = ["do", "do#", "re", "re#", "mi", "fa", "fa#", "sol", "sol#", "la", "la#", "si"];
    const NOTES_ENGLSIH = ["c", "c#", "d", "d#", "e", "f", "f#", "g", "g#", "a", "a#", "b"];

    private $modifier = null;
    private $minor = false;
    private $chord_num = -1;
    private $input_string = 'do';

    private $english = false;

    function __construct(string $chord_string)
    {
        $this->input_string = $chord_string;
        $this->minor = str_contains($chord_string, 'm');
        if (preg_match("/\d/", $chord_string, $matches)) {
            $this->modifier = $matches[0][0];
        }
        $this->chord_num = $this->parse_chord($chord_string);
    }

    function is_modified()
    {
        return !is_null($this->modifier);
    }
    function get_modifier()
    {
        return intval($this->modifier);
    }
    function is_minor()
    {
        return $this->minor;
    }
    function is_parsed()
    {
        return $this->chord_num != -1;
    }
    function get_chord_num()
    {
        return $this->chord_num;
    }
    function to_english(bool $english = true)
    {
        $this->english = $english;

        return $this;
    }

    private function parse_chord(string $chord_string)
    {
        foreach (self::NOTES_REGEX as $regex => $cn) {
            if (preg_match($regex, $chord_string)) {
                return $cn;
            }
        }
        return -1;
    }

    ##
    # Prints the chord
    # @param [Boolean] Optional boolean to set the notation (English|Spanish)
    # @return [String] String representation of the chord. If not parsed input string will be returned.
    public function __toString()
    {
        if (!$this->is_parsed())
            return $this->input_string;

        $chord = $this->english ? self::NOTES_ENGLSIH[$this->chord_num] : self::NOTES_SPANISH[$this->chord_num];
        if ($this->is_minor()) {
            $chord .= 'm';
            $chord = strtolower($chord);
        } else {
            $chord = strtoupper($chord);
        }
        if ($this->is_modified())
            $chord .= $this->modifier;

        return $chord;
    }

    ##
    # Returns the distance between this chord and other chord
    # @param [Chord] the other chord
    # @raise if self chord is not parsed (parsed?)
    # @raise if the parameter is not a Chord
    # @raise if other chord is not parsed (parsed?)
    # @return [Chord] self
    public function distance($other)
    {
        if (!$this->is_parsed()) throw new Exception('self Chord ('.$this->input_string.') not parsed!');
        if (!($other instanceof Chord)) throw new Exception('Not a Chord');
        if (!$other->is_parsed()) throw new Exception('other Chord not parsed!');

        return $other->get_chord_num() - $this->chord_num;
    }

    ##
    # Ads +semitones+ as integer to an existing parsed chord.
    # @param [Integer] the number of semitones to be added.
    # @raise if self chord is not parsed (parsed?)
    # @raise if the parameter +semitones+ is not an Integer
    # @return [Chord] self
    public function plus($other)
    {
        if (!$this->is_parsed()) throw new Exception('self Chord ('.$this->input_string.') not parsed!');
        if (!is_int($other)) throw new Exception('Expected an Integer');

        $this->chord_num = ($this->chord_num + $other) % sizeof(self::NOTES_SPANISH);

        return $this;
    }

    ##
    # Substracts +semitones+ as integer to an existing parsed chord.
    # @param [Integer] the number of semitones to be substracted.
    # @raise if self chord is not parsed (parsed?)
    # @raise if the parameter +semitones+ is not an Integer
    # @return [Chord] self
    public function minus($other)
    {
        if (!$this->is_parsed()) throw new Exception('self Chord ('.$this->input_string.') not parsed!');
        if (!is_int($other)) throw new Exception('Expected an Integer');

        $real_semitone = $other % sizeof(self::NOTES_SPANISH);
        $this->chord_num -= $real_semitone;
        if($this->chord_num < 0)
            $this->chord_num += sizeof(self::NOTES_SPANISH);

        return $this;
    }

    public function transpose(int $semitone)
    {
        if ($semitone > 0)
            return $this->plus($semitone);
        else if ($semitone < 0)
            return $this->minus(abs($semitone));
        else
            return $this;
    }

    ##
    # Compares 2 chords (the second can be string)
    # @param [Chord] the other chord
    # @return [Boolean] if self is lower
    # @raise if self chord is not parsed (parsed?)
    # @raise if the parameter is not a Chord
    # @raise if other chord is not parsed (parsed?)
    # Not part of PHP but returning -1, 0 ,1
    public function compareTo($other)
    {
        if (!$this->is_parsed()) throw new Exception('self Chord ('.$this->input_string.') not parsed!');
        if (!($other instanceof Chord) && !is_string($other)) throw new Exception('Not a Chord or String');

        $chord = is_string($other) ? new Chord($other) : $other;
        if (!$chord->is_parsed()) throw new Exception('other Chord not parsed!');

        return self::intcmp($chord->get_chord_num(), $this->chord_num);
    }

    ##
    # Compares 2 chords (the second can be string)
    # @param [Chord, String] the other chord
    # @return [Boolean] if they are equal
    # @raise if self chord is not parsed (parsed?)
    # @raise if other chord is not parsed (parsed?)
    public function equals($other)
    {
        return $this->compareTo($other) == 0;
    }

    private static function intcmp($a, $b)
    {
        if ((int)$a == (int)$b) return 0;
        if ((int)$a  > (int)$b) return 1;
        if ((int)$a  < (int)$b) return -1;
    }
}
