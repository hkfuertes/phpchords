<?php

namespace PhpChords\Chordpro;

class Line
{
  const REMOVE_BRACKETS_REGEX = "/(\[|\])/";
  const CHORD_REGEX = "/\[(?<chord>.{1,6})\]/";
  const CHORUS_KEY_WORDS = ["{start_of_chorus}", "{end_of_chorus}"];

  private $chorus;
  private $first_line;
  private $line;
  private $parsed = [];

  public function __construct(string $line, bool $first_line = false, bool $chorus = false)
  {
    $this->line = $line;
    $this->first_line = $first_line;
    $this->chorus = $chorus;
    $this->parsed = $this->parse($line);
  }

  public function is_chorus()
  {
    return $this->chorus;
  }
  public function is_first_line()
  {
    return $this->first_line;
  }
  public function is_text_only()
  {
    return sizeof($this->parsed) == 1 && is_null(array_values($this->parsed)[0]);
  }
  public function to_array()
  {
    return $this->parsed;
  }

  ##
  # Parses the line and returns a hash with the lyric and its chord
  # @return [Hash<String, String>] {'text' => 'chord' || nil}
  private function parse(string $line)
  {

    # Split trimmed line by regex, and clean null or empty (strlen == 0)
    $parts = array_values(array_filter(preg_split(self::CHORD_REGEX, trim($line)), 'strlen'));
    preg_match_all(self::CHORD_REGEX, trim($line), $matches,);
    $chords = $matches['chord'];

    if ($line[0] != '[') array_unshift($chords, null);

    $ret_val = [];
    foreach ($parts as $index => $value) {
      $ret_val[$value] = $chords[$index];
    }

    return $ret_val;
  }

  ##
  # Retruns and array of Lines. With the chords and chorus and first lines.
  # @param [String] the song_text
  # @return [Array<Line>] the lines of a song_text
  public static function generate_all(string $song_text)
  {
    $chorus = $first_line = false;
    $ret_val = [];
    foreach (explode("\r\n", $song_text) as $line_string) {

      if (self::is_special_line($line_string, $chorus)) {
        $first_line = true;
        continue;
      }

      $ret_val[] = new Line($line_string, $first_line, $chorus);
      $first_line = false;
    }

    return $ret_val;
  }

  ##
  # If special line is passed, it yeilds the chorus status.
  # @param [String] current_line
  # @param [boolean] <out> $chorus
  # @return [boolean] is_special
  private static function is_special_line(string $line_string, bool &$chorus)
  {
    $is_start = str_contains($line_string, self::CHORUS_KEY_WORDS[0]);
    $is_end = str_contains($line_string, self::CHORUS_KEY_WORDS[1]);
    $is_keyword = $is_start || $is_end;
    $is_empty = empty(trim($line_string));

    # Only if keyword, we modify the out parameter.
    if($is_keyword) $chorus = $is_start;

    return $is_empty || $is_keyword;
  }
}
