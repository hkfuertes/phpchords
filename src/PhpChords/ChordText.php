<?php

namespace PhpChords;

class ChordText
{
    const REMOVE_BRACKETS_REGEX = "/(\[|\])/";
    const ANY_CHORD_REGEX = "/\[.{1,6}\]/";

    private $input_text;
    private $modifier = 0;
    private $english = false;
    private $show_chords = true;

    public function __construct(string $text)
    {
        $this->input_text = $text;
    }

    public function to_english(bool $english = true)
    {
        $this->english = $english;

        return $this;
    }

    public function with_chords($show_chords = true)
    {
        $this->show_chords = $show_chords;

        return $this;
    }

    ##
    # Prints the song with the modifiers. Overrides the to string function
    # By default it prints WITH chords and in SPANISH notation
    # @return [String] the song
    public function __toString()
    {
        return preg_replace_callback(self::ANY_CHORD_REGEX, function ($matches) {
            if ($this->show_chords) {
                $chord = new Chord(preg_replace(self::REMOVE_BRACKETS_REGEX, '', $matches[0]));
                return '[' . $chord->transpose($this->modifier)->to_english($this->english) . ']';
            } else
                return '';
        }, $this->input_text);
    }

    ##
    # Gets the first note (and so tone of the song)
    # @return [Chord] the first chord of the song
    public function get_tone()
    {
        if (preg_match(self::ANY_CHORD_REGEX, $this->input_text, $matches)) {
            return new Chord(preg_replace(self::REMOVE_BRACKETS_REGEX, '', $matches[0]));
        } else
            return new Chord("N/A");
    }

    ##
    # Modifies the chords of the song
    # @param [Integer, Chord] the number of semitones or the destination tone
    # @return [ChordText] self
    public function transpose($other)
    {
        if (!is_null($this->get_tone()))
            if ($other instanceof Chord) {
                $this->modifier = $this->get_tone()->distance($other);
            } else if (is_int($other)) {
                $this->modifier = $other;
            }

        return $this;
    }
}
