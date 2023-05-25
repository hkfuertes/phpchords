# frozen_string_literal: true

module Chordpro
  module Printers
    ##
    # Static class parent of all printers.
    class BasePrinter
      def self.print
        raise 'Not Implemented!'
      end
    end
  end
end
