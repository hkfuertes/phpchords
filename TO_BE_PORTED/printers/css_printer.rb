# frozen_string_literal: true

module Chordpro
  module Printers
    ##
    # Printer of a song using the new css template
    class CssPrinter < BasePrinter
      class << self
        # chorus, data- attributes, part
        TEMPLATE = '<span class="%s"%s>%s</span>'
        BREAK_TEMPLATE = '<br/>'
        SPACE_TEMPLATE = '&nbsp;'

        def print(song_text)
          Line.generate_all(song_text).each_with_index.map do |line|
            line.to_h.values.map do |value|
              text, chord = value
              format(TEMPLATE, classes(line.chorus?, chord), data_chord(chord), padded_text(text, chord))
            end.join
          end.join("#{BREAK_TEMPLATE}\n") + BREAK_TEMPLATE
        end

        private

        def padded_text(text, chord)
          if !chord.nil? && text.length < chord.length
            return text + (SPACE_TEMPLATE * ((chord.length - text.length) + 2))
          end

          text
        end

        def classes(chorus, chord)
          ret = chord ? 'lyric' : ''
          ret += ' chord' if chord
          ret += ' chorus' if chorus

          ret
        end

        def data_chord(chord)
          return '' if chord.nil?

          " data-chord='#{chord}' data-orig-chord='#{chord}'"
        end
      end
    end
  end
end
