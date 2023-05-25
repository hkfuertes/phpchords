<?php

namespace spec\PhpChords\Printers;

use PhpChords\Printers\CssPrinter;
use PhpSpec\ObjectBehavior;

class CssPrinterSpec extends ObjectBehavior
{
    function it_prints_when_line_has_chords()
    {
        $text = "{start_of_chorus}\r\n[do]Vine a ala[Sol]bar a [lam]Dios\r\n{end_of_chorus}\r\nVine a ale[Sol]bar a [lam]Dios";
        $output = "<span class='lyric chord chorus' data-chord='do' data-orig-chord='do'>Vine a ala</span>" .
            "<span class='lyric chord chorus' data-chord='Sol' data-orig-chord='Sol'>bar a </span>" .
            "<span class='lyric chord chorus' data-chord='lam' data-orig-chord='lam'>Dios</span>" . '<br/>\n' .
            "<span>Vine a ale</span>" .
            "<span class='lyric chord' data-chord='Sol' data-orig-chord='Sol'>bar a </span>" .
            "<span class='lyric chord' data-chord='lam' data-orig-chord='lam'>Dios</span>";
        $this->static_print($text)->shouldReturn($output);
    }

    function it_prints_when_line_has_no_chords()
    {
        $text = "{start_of_chorus}\r\nVine a alabar a Dios\r\n{end_of_chorus}\r\nVine a ale[Sol]bar a [lam]Dios";
        $output = "<span class='chorus'>Vine a alabar a Dios</span>" . '<br/>\n' .
            "<span>Vine a ale</span>" .
            "<span class='lyric chord' data-chord='Sol' data-orig-chord='Sol'>bar a </span>" .
            "<span class='lyric chord' data-chord='lam' data-orig-chord='lam'>Dios</span>";
        $this->static_print($text)->shouldReturn($output);
    }

    function it_prints_when_line_does_not_start_with_chords()
    {
        $text = "María, [DO]mírame, [SOL]María, [lam]mírame";
        $output = "<span>María, </span>" .
            "<span class='lyric chord' data-chord='DO' data-orig-chord='DO'>mírame, </span>" .
            "<span class='lyric chord' data-chord='SOL' data-orig-chord='SOL'>María, </span>" .
            "<span class='lyric chord' data-chord='lam' data-orig-chord='lam'>mírame</span>";
        $this->static_print($text)->shouldReturn($output);
    }
}
