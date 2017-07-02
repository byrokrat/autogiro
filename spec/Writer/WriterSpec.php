<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\Writer;
use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Writer\PrintingVisitor;
use byrokrat\autogiro\Writer\Output;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Intervals;
use byrokrat\banking\AccountNumber;
use byrokrat\id\Id;
use byrokrat\amount\Currency\SEK;
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
        $this->acceptDigitalMandate('foobar');
        $treeBuilder->addAcceptDigitalMandateRecord('foobar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_reject_mandate($treeBuilder)
    {
        $this->rejectDigitalMandate('foobar');
        $treeBuilder->addRejectDigitalMandateRecord('foobar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_update_mandate($treeBuilder)
    {
        $this->updateMandate('foo', 'bar');
        $treeBuilder->addUpdateMandateRecord('foo', 'bar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_add_transaction($treeBuilder, SEK $amount, \DateTime $date)
    {
        $this->addTransaction('foo', $amount, $date, 'ref', '10', 100);
        $treeBuilder->addIncomingTransactionRecord('foo', $amount, $date, 'ref', '10', 100)->shouldHaveBeenCalled();
    }

    function it_defaults_to_creating_one_time_transactions($treeBuilder, SEK $amount, \DateTime $date)
    {
        $this->addTransaction('foo', $amount, $date);
        $treeBuilder->addIncomingTransactionRecord('foo', $amount, $date, '', Intervals::INTERVAL_ONCE, 0)->shouldHaveBeenCalled();
    }

    function it_creates_monthly_transactions($treeBuilder, SEK $amount, \DateTime $date)
    {
        $this->addMonthlyTransaction('foo', $amount, $date, 'ref');
        $treeBuilder->addIncomingTransactionRecord('foo', $amount, $date, 'ref', Intervals::INTERVAL_MONTHLY_ON_DATE, 0)->shouldHaveBeenCalled();
    }

    function it_creates_immediate_transactions($treeBuilder, SEK $amount)
    {
        $this->addImmediateTransaction('foo', $amount, 'ref');
        $treeBuilder->addImmediateIncomingTransactionRecord('foo', $amount, 'ref')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_add_outgoing_transaction($treeBuilder, SEK $amount, \DateTime $date)
    {
        $this->addOutgoingTransaction('foo', $amount, $date, 'ref', '10', 100);
        $treeBuilder->addOutgoingTransactionRecord('foo', $amount, $date, 'ref', '10', 100)->shouldHaveBeenCalled();
    }

    function it_creates_immediate_outgoing_transactions($treeBuilder, SEK $amount)
    {
        $this->addImmediateOutgoingTransaction('foo', $amount, 'ref');
        $treeBuilder->addImmediateOutgoingTransactionRecord('foo', $amount, 'ref')->shouldHaveBeenCalled();
    }
}
