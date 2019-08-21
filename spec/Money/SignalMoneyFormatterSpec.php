<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Money;

use byrokrat\autogiro\Money\SignalMoneyFormatter;
use byrokrat\autogiro\Exception\RuntimeException;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SignalMoneyFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SignalMoneyFormatter::CLASS);
    }

    function it_is_a_money_formatter()
    {
        $this->shouldHaveType(MoneyFormatter::CLASS);
    }

    function it_fails_on_non_sek_money()
    {
        $this->shouldThrow(RuntimeException::class)->duringFormat(Money::EUR(100));
    }

    function it_formats_regular_money()
    {
        $this->format(Money::SEK(100))->shouldReturn('100');
    }

    function it_formats_signaled_money()
    {
        $this->format(Money::SEK(-100))->shouldReturn('10å');
    }
}
