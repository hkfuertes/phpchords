<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Chord;

class ChordSpec extends ObjectBehavior
{
    function it_parses_the_chord_when_chord_is_passed(){
        $this->beConstructedWith('do#m7');

        $this->get_chord_num()->shouldReturn(1);
        $this->is_parsed()->shouldReturn(true);
        $this->is_minor()->shouldReturn(true);
        $this->is_modified()->shouldReturn(true);
        $this->get_modifier()->shouldReturn(7);
    }

    function it_does_not_parses_the_chord_when_not_a_chord_passed(){
        $this->beConstructedWith('ja');

        $this->get_chord_num()->shouldReturn(-1);
        $this->is_parsed()->shouldReturn(false);
    }

    function it_adds_less_than_11_semitones(){
        $this->beConstructedWith('Do#');

        $this->plus(4)->get_chord_num()->shouldReturn(5);
    }

    function it_adds_more_than_11_semitones(){
        $this->beConstructedWith('Do#');

        $this->plus(15)->get_chord_num()->shouldReturn(4);
    }

    function it_substracts_less_than_11_semitones(){
        $this->beConstructedWith('G#');

        $this->minus(3)->get_chord_num()->shouldReturn(5);
    }

    function it_substracts__more_than_11_semitones(){
        $this->beConstructedWith('Do#');

        $this->minus(15)->get_chord_num()->shouldReturn(10);
    }

    function it_throws_an_exception_if_object_is_not_a_chord(){
        $this->beConstructedWith('f');

        $this->shouldThrow(new \Exception('Not a Chord'))->during('distance',['bla']);
        $this->shouldThrow(new \Exception('Not a Chord'))->during('distance',[5]);
    }

    function it_calculates_distance_when_lower_chord_is_passed(){
        $this->beConstructedWith('f');
        $other = new Chord('c#');

        $this->distance($other)->shouldReturn(-4);
    }

    function it_calculates_distance_when_higher_chord_is_passed(){
        $this->beConstructedWith('f');
        $other = new Chord('sol');

        $this->distance($other)->shouldReturn(2);
    }

    function it_prints_the_minor_chord(){
        $this->beConstructedWith('do#m7');

        $this->__toString()->shouldReturn('do#m7');
        $this->to_english()->__toString()->shouldReturn('c#m7');
    }

    function it_prints_the_major_chord(){
        $this->beConstructedWith('do7');

        $this->__toString()->shouldReturn('DO7');
        $this->to_english()->__toString()->shouldReturn('C7');
    }

    function it_return_true_when_same_chord_is_compared() {
        $this->beConstructedWith('do#m7');
        $other = new Chord('c#m7');

        $this->equals($other)->shouldReturn(true);
    }

    function it_return_false_when_diferent_chord_is_compared() {
        $this->beConstructedWith('do#m7');

        $this->equals('d#')->shouldReturn(false);
    }

    function it_throws_an_excption_if_not_string_or_chord_is_passed(){
        $this->beConstructedWith('c#m7');

        $this->shouldThrow(new \Exception('Not a Chord or String'))->during('equals',[3]);
    }

    function it_is_greater_than(){
        $this->beConstructedWith('g#m7');

        $this->compareTo('fm')->shouldReturn(-1);
        $this->compareTo('a#')->shouldReturn(1);
    }

    function it_is_lower_than(){
        $this->beConstructedWith('g#m7');

        $this->compareTo('am')->shouldReturn(1);
        $this->compareTo('f#')->shouldReturn(-1);
    }

    function it_throws_exceptions_on_all_operations_when_chord_is_not_parsed(){
        $this->beConstructedWith('xat7m#');
        $other = new Chord('c');

        $this->__toString()->shouldReturn('xat7m#');

        $this->shouldThrow(new \Exception('self Chord (xat7m#) not parsed!'))->during('plus',[1]);
        $this->shouldThrow(new \Exception('self Chord (xat7m#) not parsed!'))->during('minus',[1]);
        $this->shouldThrow(new \Exception('self Chord (xat7m#) not parsed!'))->during('equals',[$other]);
        $this->shouldThrow(new \Exception('self Chord (xat7m#) not parsed!'))->during('distance',[$other]);
        $this->shouldThrow(new \Exception('self Chord (xat7m#) not parsed!'))->during('compareTo',[$other]);
    }

    function it_throws_exceptions_on_all_operations_when_other_chord_is_not_parsed(){
        $this->beConstructedWith('c#');
        $other = new Chord('xat7m#');

        $this->shouldThrow(new \Exception('other Chord not parsed!'))->during('equals',[$other]);
        $this->shouldThrow(new \Exception('other Chord not parsed!'))->during('distance',[$other]);
        $this->shouldThrow(new \Exception('other Chord not parsed!'))->during('compareTo',[$other]);
    }
}
