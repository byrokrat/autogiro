<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\Writer;
use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Writer\PrintingVisitor;
use byrokrat\autogiro\Writer\Output;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\banking\AccountNumber;
use byrokrat\id\Id;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WriterSpec extends ObjectBehavior
{
    function let(TreeBuilder $treeBuilder, PrintingVisitor $printer, Visitor $visitor)
    {
        $this->beConstructedWith($treeBuilder, $printer, $visitor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Writer::CLASS);
    }

    function it_can_create_content($treeBuilder, $printer, $visitor, FileNode $tree)
    {
        $treeBuilder->buildTree()->willReturn($tree)->shouldBeCalled();
        $tree->accept($visitor)->shouldBeCalled();
        $printer->setOutput(Argument::type(Output::CLASS))->shouldBeCalled();
        $tree->accept($printer)->shouldBeCalled();
        $this->getContent()->shouldEqual('');
    }

    function it_calls_tree_builder_on_reset($treeBuilder)
    {
        $this->reset();
        $treeBuilder->reset()->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_new_mandate($treeBuilder, AccountNumber $account, Id $id)
    {
        $this->addNewMandate('foobar', $account, $id);
        $treeBuilder->addCreateMandateRecord('foobar', $account, $id)->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_delete_mandate($treeBuilder)
    {
        $this->deleteMandate('foobar');
        $treeBuilder->addDeleteMandateRecord('foobar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_accept_mandate($treeBuilder)
    {
        $this->acceptMandate('foobar');
        $treeBuilder->addAcceptMandateRecord('foobar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_reject_mandate($treeBuilder)
    {
        $this->rejectMandate('foobar');
        $treeBuilder->addRejectMandateRecord('foobar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_update_mandate($treeBuilder)
    {
        $this->updateMandate('foo', 'bar');
        $treeBuilder->addUpdateMandateRecord('foo', 'bar')->shouldHaveBeenCalled();
    }
}
