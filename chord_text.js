export default class ChordText {

    SETS = {
        'ENGLISH': ["c", "c#", "d", "d#", "e", "f", "f#", "g", "g#", "a", "a#", "b"],
        'SPANISH': ["do", "do#", "re", "re#", "mi", "fa", "fa#", "sol", "sol#", "la", "la#", "si"]
    }

    modifier = 0; node; created;
    english = false;

    constructor(input) {
        this.node = (input instanceof (Element)) ? input : this.create_node(input);
        this.created = !(input instanceof (Element));
    }

    add() {
        this.modifier++;
        this.modifyNode();
    }

    substract() {
        this.modifier--;
        this.modifyNode();
    }

    zero() {
        this.modifier = 0;
        this.modifyNode();
    }

    toggle() {
        this.english = !this.english;
        this.modifyNode();
    }

    created() {
        return this.created;
    }

    re_create_node(input) {
        this.node = this.create_node(input)
        this.modifyNode()
    }

    get_node() {
        return this.node;
    }

    static addChordStyles() {
        let style = document.createElement("style")
        style.innerHTML = `
          .chorus { font-weight: bold; }
          .lyric { line-height: 2.7; white-space: pre;  }
          .song_hidden { display: none; }
          
          .chord {
            position: relative;
            display: inline-block;
          }
          
          .chord:before {
            content: attr(data-chord);
            position: absolute;
            top: -1rem;
            color: #001f3f;
          }`
        document.head.appendChild(style);
    }

    // TODO: Review the minus...
    modifyNode() {
        this.node.querySelectorAll('.chord').forEach((el) => {
            let chord = el.getAttribute('data-orig-chord');
            let current_index = this._calculateCurrentIndex(chord);
            let new_index = this._calculateNewIndex(current_index);
            let new_chord = this.english ? this.SETS['ENGLISH'][new_index] : this.SETS['SPANISH'][new_index];

            if (chord.includes('m')) {
                new_chord += 'm';
            } else {
                new_chord = new_chord.toUpperCase();
            }
            if (chord.includes('7')) {
                new_chord += '7';
            }

            el.setAttribute('data-chord', new_chord);
        })
    }

    _calculateCurrentIndex(chord_string) {
        for (let [i, c] of this.SETS['SPANISH'].entries()) {
            if (chord_string.toLowerCase().includes(c)) {
                if (chord_string == 'lam') console.log(i)
                return i
            }
        }
        for (let [i, c] of this.SETS['ENGLISH'].entries()) {
            if (chord_string.toLowerCase().includes(c)) {
                return i
            }
        }
        return 0;
    }

    _calculateNewIndex(current_index) {
        const l = this.SETS['ENGLISH'].length;
        if (this.modifier == 0) return current_index;

        let real_distance = Math.abs(this.modifier) % l;

        if (this.modifier > 0) {
            return (current_index + real_distance) % l;
        } else {
            return ((current_index - real_distance) + l) % l;
        }
    }


    create_node(input) {
        let parts = this._parseSongText(input);
        let container = document.createElement('div');

        parts.forEach((line) => {
            line.forEach((part) => {
                let partNode = document.createElement('span');
                if (part['chord']) {
                    partNode.setAttribute('class', part['chorus'] ? 'lyric chord chorus' : 'lyric chord');
                    partNode.setAttribute('data-chord', part['chord']);
                    partNode.setAttribute('data-orig-chord', part['chord']);
                    partNode.innerHTML = this._paddedText(part['part'], part['chord']);
                } else {
                    partNode.setAttribute('class', part['chorus'] ? 'chorus' : '');
                    partNode.innerHTML = part['part'];
                }
                container.appendChild(partNode);
            })
            container.appendChild(document.createElement('br'));
        })
        return container;
    }

    _paddedText(part, chord) {
        if ((chord.length + 2) > part.length) {
            let max = (chord.length - part.length) + 3;
            return part + (new Array(max)).join(' ')
        } else {
            return part;
        }
    }

    _emptyPart() {
        return [{
            chord: null,
            chorus: false,
            part: '&nbsp;'
        }];
    }

    _parseSongText(song_text) {
        const CHORD_REGEX = /\[.{1,6}\]/g
        let chorus = false;
        return song_text.split(/\n/g).map((line) => {
            if (!line) return this._emptyPart(true);
            if (line.includes('{start_of_chorus}')) {
                chorus = true;
                return this._emptyPart();
            }

            if (line.includes('{end_of_chorus}')) {
                chorus = false;
                return this._emptyPart();
            }

            let parts_for_line = line.trim()
                .split(CHORD_REGEX)
                .filter(item => item != "")
            let chords_for_line = [...line.matchAll(new RegExp(CHORD_REGEX))].flat()

            if (line[0] != '[')
                chords_for_line.unshift(null)

            return chords_for_line.map((e, i) => {
                return {
                    chord: e ? e.replace('[', '').replace(']', '') : e,
                    chorus: chorus,
                    part: parts_for_line[i] || '&nbsp;'
                }
            })
        }).filter(item => item != null)
    }
}