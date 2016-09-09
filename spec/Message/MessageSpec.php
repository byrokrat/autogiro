<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Message;

use byrokrat\autogiro\Message\Message;
use PhpSpec\ObjectBehavior;

class MessageSpec extends ObjectBehavior
{
    const CODE = '73.02';
    const MESSAGE = 'foobarbaz';

    function let()
    {
        $this->beConstructedWith(self::CODE, self::MESSAGE);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Message::CLASS);
    }

    function it_should_contain_a_code()
    {
        $this->getCode()->shouldEqual(self::CODE);
    }

    function it_should_contain_a_message()
    {
        $this->getMessage()->shouldEqual(self::MESSAGE);
    }

    function it_should_cast_to_string()
    {
        $this->__tostring()->shouldEqual(self::MESSAGE);
    }
}
