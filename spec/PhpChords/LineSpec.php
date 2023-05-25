<?php

namespace spec\PhpChords;

use PhpSpec\ObjectBehavior;
class LineSpec extends ObjectBehavior
{
    function it_starts_with_chord()
    {
        $this->beConstructedWith('[do]Vine a ala[Sol]bar a [lam]Dios');
        $ret_val = [
            'Vine a ala' => 'do',
            'bar a ' => 'Sol',
            'Dios' => 'lam'
        ];

        $this->is_text_only()->shouldReturn(False);
        $this->to_array()->shouldBeEqualTo($ret_val);
    }

    function it_does_not_starts_with_chord()
    {
        $this->beConstructedWith('Vine a ala[Sol]bar a [lam]Dios');
        $ret_val = [
            'Vine a ala' => null,
            'bar a ' => 'Sol',
            'Dios' => 'lam'
        ];

        $this->is_text_only()->shouldReturn(False);
        $this->to_array()->shouldBeEqualTo($ret_val);
    }
    function it_has_no_chord()
    {
        $this->beConstructedWith('Vine a alabar a Dios');
        $ret_val = ['Vine a alabar a Dios' => null];

        $this->is_text_only()->shouldReturn(True);
        $this->to_array()->shouldBeEqualTo($ret_val);
    }
}


/*

 */