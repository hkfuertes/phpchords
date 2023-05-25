<?php

namespace spec\PhpChords\Chordpro;

use PhpSpec\ObjectBehavior;
use PhpChords\Chordpro\ChordText;
use PhpChords\Chordpro\Chord;

class ChordTextSpec extends ObjectBehavior
{
    const SONG_TEXT = 'Est[do]o [d#]es un ejem[Gm7]plo.';

    function it_prints_the_song(){
        $this->beConstructedWith(self::SONG_TEXT);

        $this->__toString()->shouldReturn('Est[DO]o [RE#]es un ejem[solm7]plo.');
        $this->to_english()->__toString()->shouldReturn('Est[C]o [D#]es un ejem[gm7]plo.');
    }

    function it_transposes_up(){
        $this->beConstructedWith(self::SONG_TEXT);
        $transposed = 'Est[RE]o [FA]es un ejem[lam7]plo.';
        $other = new Chord('d');

        $this->transpose(2)->__toString()->shouldReturn($transposed);
        $this->transpose($other)->__toString()->shouldReturn($transposed);
    }

    function it_transposes_down(){
        $this->beConstructedWith(self::SONG_TEXT);
        $transposed = 'Est[LA#]o [DO#]es un ejem[fam7]plo.';
        $other = new Chord('a#');

        $this->transpose(-2)->__toString()->shouldReturn($transposed);
        $this->transpose($other)->__toString()->shouldReturn($transposed);
    }

    function it_gets_the_tone(){
        $this->beConstructedWith(self::SONG_TEXT);

        $this->get_tone()->__toString()->shouldReturn('DO');
    }


}
