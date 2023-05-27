import { Controller } from "@hotwired/stimulus"
import ChordText from "chord_text"

// Connects to data-controller="chords"
export default class extends Controller {
  static targets = ['rawSongText', 'formattedSongText'];
  chordSet;


  connect() {
    ChordText.addChordStyles()
    if (this.hasRawSongTextTarget) {
      let input_text = this.rawSongTextTarget.nodeName == "TEXTAREA" ? this.rawSongTextTarget.value : this.rawSongTextTarget.innerHTML
      this.chordSet = new ChordText(input_text);
    } else {
      this.chordSet = new ChordText(this.formattedSongTextTarget);
    }
    if (this.chordSet.created)
      this.paint();
  }

  paint() {
    this.formattedSongTextTarget.innerHTML = null;
    this.formattedSongTextTarget.appendChild(this.chordSet.get_node());
  }

  add() {
    this.chordSet.add();
  }

  substract() {
    this.chordSet.substract();
  }

  zero() {
    this.chordSet.zero();
  }

  translate() {
    this.chordSet.toggle();
  }

  edit(e){
    this.chordSet.re_create_node(e.target.value);
    this.paint();
  }
}
