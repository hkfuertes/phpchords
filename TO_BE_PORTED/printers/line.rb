# frozen_string_literal: true

module Chordpro
  module Printers
    ##
    # Representation of a chunk
    class Line
      REMOVE_BRACKETS_REGEX = /(\[|\])/
      CHORD_REGEX = /\[.{1,6}\]/
      CHORUS_KEY_WORDS = %w[{start_of_chorus} {end_of_chorus}].freeze

      attr_reader :chord, :text

      define_method(:chorus?) { @chorus }
      define_method(:text_only?) { @parsed.keys.length == 1 }
      define_method(:to_h) { @parsed }

      def initialize(line, chorus: false)
        @chorus = chorus
        @line = line
        @parsed = parse(line)
      end

      private

      ##
      # Parses the line and returns a hash with the lyric and its chord
      # @return [Hash<String, String?>] {'text' => 'chord' || nil}
      def parse(line)
        parts = line.strip.split(CHORD_REGEX).delete_if(&:blank?) # ['vine a ala','bar a', 'dios']
        chords = line.strip.scan(CHORD_REGEX) # [do, re, sol]
        chords.unshift(nil) unless line[0] == '[' # [nil, do, re, sol]

        # { 'vine a ala' => 'do', 'bar a' => 'sol', 'Dios' => 'lam'} -> 'text' => 'chord' || nil
        parts.each_with_index.to_h { |part, index| [index, [part, chords[index]&.gsub(REMOVE_BRACKETS_REGEX, '')]] }
      end

      class << self
        ##
        # Retruns and array of Lines. With the chords and chorus and first lines.
        # @param [String] the song_text
        # @return [Array<Line>] the lines of a song_text
        def generate_all(song_text)
          chorus = false
          song_text.split("\r\n").map do |line|
            next if line.empty?
            next Line.new('', chorus:) if special_line?(line) { |c| chorus = c }

            # Block runs at the end of initialization (to set the variable to false in the same line)
            Line.new(line, chorus:)
          end.delete_if(&:blank?)
        end

        private

        ##
        # If special line is passed, it yeilds the chorus status.
        # @param [String] current_line
        # @return [boolean] special? if &block not given
        # @yield [boolean] chorus if &block given and is special?
        def special_line?(line)
          yield (line.include? 'start') if block_given? && CHORUS_KEY_WORDS.include?(line.strip)

          CHORUS_KEY_WORDS.include?(line.strip)
        end
      end
    end
  end
end
