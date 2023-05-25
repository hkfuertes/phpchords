# PHPChords Library
Simple library writen in php that manipulates chordpro like song data. It allows to change the chords (up and down) and also translate them between english and spanish. Additionally there is a line splitter, preprocessing for other steps down a posible scenario pipeline.

Chordpro-like example:
```
[c]this is an e[d]xample of [g#m]chord[em]pro
```

## Instalation
This library is provided as a `composer` package. You would need to add the repository to the `composer.json` file of your main project:
```jsonc
//...
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/hkfuertes/phpchords.git"
    }
],
//...
"require": {
    //...
    "phpchords/chordpro": "dev-main"
    //...
},
//...
```
Then run a `composer install` to add it into your project.

## Usage
3 classes are provided. `Chord`, `ChordText`, `Line`.
### Chord
A representation of a chord. It will try to parse the given string input. Afterwards operations can be performed on that chord.
```php
<?php
use PhpChords\Chord;

$chord = new Chrod('c#m');
$chord2 = new Chord('dm');

$chord->is_modified(); // false, c#m7 will return true because of the 7.
$chord->get_modifier(); //0, c#m7 will return 7.
$chord->is_minor(); //true
$chord->is_parsed(); //true as the chord is recognized.
$chord->to_english(); //sets the chord to be printed in english (A,B,C rather than LA, SI, DO)
$chord->__toString(); //Prints the final chord, with all the modifications (english?, and posible ups and downs)
$chord->distance($chord2); //1 as there is only 1 semitone of difference. Distance can be negative.
$chord->plus(1); //Sets the chord to be 'dm' when the __toString() is called.
$chord->minus(1); //Sets the chord to be 'cm' when the __toString() is called.
$chord->transpose(-1) //sets the chord to be 'cm' when the __toString() is called.
$chord->compareTo($chord2); //Comparator [-1,0,1] --> 1
$chord->compareTo('cm'); //Comparator [-1,0,1] --> -1
$chord->equal($chord2); //false
$chord->equal('c#m'); //true
```
Please refer to the [spec](spec/Chordpro/ChordSpec.php) to see more examples.
### ChordText
A representation of a whole text. All the operations will be printed/viewed upon `__toString()`:
```php
<?php
use PhpChords\Chord;
use PhpChords\ChordText;

$text = '[c]song to be wri[d]tten';
$chord = new Chord('e');

$chordText = new ChordText($text);
$chordText->to_english(); //sets the chords to be printed in english (A,B,C rather than LA, SI, DO)
$chordText->with_chords(false); //sets the chords not to be printed
$chordText->get_tone(); // Chord('c') is retuned
$chordText->transpose($chord); //transposes to: '[e]song to be wri[f#]tten'
$chordText->transpose(2); //transposes to: '[d]song to be wri[e]tten'
```
Please refer to the [spec](spec/Chordpro/ChordTextSpec.php) to see more examples.
### Line
A abstract representation of a line, splitted into an array. Please refer to the [spec](spec/Chordpro/LineSpec.php) to see examples.

## TODO
- Documentation on `chord_text.js`
- CSS generator
