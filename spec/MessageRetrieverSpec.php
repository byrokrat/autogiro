<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro;

use byrokrat\autogiro\MessageRetriever;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageRetrieverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MessageRetriever::class);
    }

    function it_reads_message_from_single_id()
    {
        $this->beConstructedWith(['key' => 'value']);
        $this->readMessage('key')->shouldReturn('value');
    }

    function it_returns_empty_string_on_no_string_content()
    {
        $this->beConstructedWith(['key' => []]);
        $this->readMessage('key')->shouldReturn('');
    }

    function it_returns_empty_string_on_missing_key()
    {
        $this->beConstructedWith([]);
        $this->readMessage('key')->shouldReturn('');
    }

    function it_reads_message_from_multiple_ids()
    {
        $this->beConstructedWith(['foo' => ['bar' => 'baz']]);
        $this->readMessage('foo', 'bar')->shouldReturn('baz');
    }

    function it_returns_empty_string_on_too_many_keys()
    {
        $this->beConstructedWith(['key' => 'value']);
        $this->readMessage('key', 'this-key-does-not-exist')->shouldReturn('');
    }

    function it_honours_wildcard_keys()
    {
        $this->beConstructedWith(['*' => ['bar' => 'baz']]);
        $this->readMessage('foo', 'bar')->shouldReturn('baz');
    }

    function it_reads_regular_key_before_wildcard()
    {
        $this->beConstructedWith(['*' => 'foo', 'bar' => 'baz']);
        $this->readMessage('bar')->shouldReturn('baz');
    }

    function it_reads_multilayerd_wildcards()
    {
        $this->beConstructedWith([
            'A' => [
                'B' => [
                    'C' => 'ABC'
                ]
            ],
            '*' => [
                '*' => [
                    'C' => '**C'
                ]
            ],
        ]);

        $this->readMessage('A', 'not-B', 'C')->shouldReturn('**C');
    }
}
