<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\MessageVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\MessageRetriever;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Text;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj, MessageRetriever $messages)
    {
        $this->beConstructedWith($errorObj, $messages);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MessageVisitor::CLASS);
    }

    function it_fails_on_unvalid_message(Node $file, Node $record, Node $msg, $errorObj, $messages)
    {
        $file->getName()->willReturn('file');
        $record->getName()->willReturn('record');
        $msg->getLineNr()->willReturn(1);
        $msg->getName()->willReturn('msg');
        $msg->getValueFrom('Number')->willReturn('code');

        $messages->readMessage('file', 'record', 'msg', 'code')->willReturn('');

        $this->beforeAutogiroFile($file);
        $this->beforeRecord($record);
        $this->beforeMessage($msg);


        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_messages(Node $file, Node $record, Node $msg, $errorObj, $messages)
    {
        $file->getName()->willReturn('file');
        $record->getName()->willReturn('record');
        $msg->getLineNr()->willReturn(1);
        $msg->getName()->willReturn('msg');
        $msg->getValueFrom('Number')->willReturn('code');

        $messages->readMessage('file', 'record', 'msg', 'code')->willReturn('message');

        $msg->addChild(Argument::that(function (Text $text) {
            return $text->getValue() == 'message';
        }))->shouldBeCalled();

        $this->beforeAutogiroFile($file);
        $this->beforeRecord($record);
        $this->beforeMessage($msg);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_defaults_to_empty_file_and_record_names(Node $msg, $messages)
    {
        $msg->getLineNr()->willReturn(1);
        $msg->getName()->willReturn('msg');
        $msg->getValueFrom('Number')->willReturn('code');
        $msg->addChild(Argument::any())->shouldBeCalled();

        $messages->readMessage('', '', 'msg', 'code')->shouldBeCalled()->willReturn('foobar');

        $this->beforeMessage($msg);
    }

    function it_resets_record_name(Node $file, Node $record, Node $msg, $messages)
    {
        $file->getName()->willReturn('file');
        $record->getName()->willReturn('record');
        $msg->getLineNr()->willReturn(1);
        $msg->getName()->willReturn('msg');
        $msg->getValueFrom('Number')->willReturn('code');
        $msg->addChild(Argument::any())->shouldBeCalled();

        $messages->readMessage('file', '', 'msg', 'code')->shouldBeCalled()->willReturn('message');

        $this->beforeRecord($record);
        $this->beforeAutogiroFile($file);
        $this->beforeMessage($msg);
    }
}
