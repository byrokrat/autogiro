<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Money;

use byrokrat\autogiro\Money\SignalMoneyParser;
use byrokrat\autogiro\Exception\RuntimeException;
use Money\Money;
use Money\MoneyParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SignalMoneyParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SignalMoneyParser::CLASS);
    }

    function it_is_a_money_parser()
    {
        $this->shouldHaveType(MoneyParser::CLASS);
    }

    function it_fails_on_non_string()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringParse(null);
    }

    function it_fails_on_empty_string()
    {
        $this->shouldThrow(RuntimeException::class)->duringParse('');
    }

    function it_creates_regular_money()
    {
        $this->parse('100')->shouldReturnSEK('100');
    }

    function it_creates_signaled_money()
    {
        $this->parse('10å')->shouldReturnSEK('-100');
    }

    function it_trims_leading_zeros()
    {
        $this->parse('00100')->shouldReturnSEK('100');
    }

    function it_defaults_to_zero()
    {
        $this->parse('0000')->shouldReturnSEK('0');
    }

    function it_fails_on_invalid_signal()
    {
        $this->shouldThrow(RuntimeException::class)->duringParse('10j');
    }

    function it_fails_on_dubble_negation()
    {
        $this->shouldThrow(RuntimeException::class)->duringParse('-10å');
    }

    public function getMatchers(): array
    {
        return [
            'returnSEK' => function (Money $money, string $expected) {
                return $money->equals(Money::SEK($expected));
            },
        ];
    }
}
