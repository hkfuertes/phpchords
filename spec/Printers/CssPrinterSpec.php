<?php

namespace spec\Printers;

use Printers\CssPrinter;
use PhpSpec\ObjectBehavior;

class CssPrinterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CssPrinter::class);
    }
}
