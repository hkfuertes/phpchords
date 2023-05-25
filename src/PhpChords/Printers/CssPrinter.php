<?php

namespace PhpChords\Printers;

class CssPrinter
{
    const BREAK_TEMPLATE = '<br/>\n';
    const SPACE_TEMPLATE = '&nbsp;';

    // Workaround to spec it!
    public function static_print(string $song_text)
    {
        return self::print($song_text);
    }

    public static function print(string $song_text)
    {
        $lines = Line::generate_all($song_text);
        $html_lines =  array_map(function ($line) {
            $html_line = array_map(function ($part_chord) use ($line) {
                list($part, $chord) = $part_chord;
                $classes = self::_clasesAttribute($chord, $line->is_chorus());
                $chords = self::_chordAttributes($chord);
                return self::_format($classes, $chords, $part);
            }, $line->to_array());
            return implode($html_line);
        }, $lines);

        return implode(self::BREAK_TEMPLATE, $html_lines);
    }

    private static function _clasesAttribute($chord, bool $chorus)
    {
        $classes = [];
        if ($chord != null)
            $classes[] = "lyric chord";
        if ($chorus)
            $classes[] = "chorus";

        return count($classes) == 0 ? '' : " class='" . trim(implode(' ', $classes)) . "'";
    }

    private static function _chordAttributes($chord)
    {
        return $chord ? " data-chord='{$chord}' data-orig-chord='{$chord}'" : '';
    }

    private static function _format($classes, $chords, $part)
    {
        return "<span{$classes}{$chords}>{$part}</span>";
    }
}
