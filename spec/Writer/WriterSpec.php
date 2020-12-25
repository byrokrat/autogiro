<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\Writer;
use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Writer\PrintingVisitor;
use byrokrat\autogiro\Writer\Output;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Intervals;
use byrokrat\banking\AccountNumber;
use byrokrat\id\IdInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WriterSpec extends ObjectBehavior
{
    function let(TreeBuilder $treeBuilder, PrintingVisitor $printer)
    {
        $this->beConstructedWith($treeBuilder, $printer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Writer::class);
    }

    function it_can_create_content($treeBuilder, $printer, Node $tree)
    {
        $treeBuilder->buildTree()->willReturn($tree)->shouldBeCalled();
        $printer->setOutput(Argument::type(Output::class))->shouldBeCalled();
        $tree->accept($printer)->shouldBeCalled();
        $this->getContent()->shouldEqual('');
    }

    function it_calls_tree_builder_on_reset($treeBuilder)
    {
        $this->reset();
        $treeBuilder->reset()->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_new_mandate($treeBuilder, AccountNumber $account, IdInterface $id)
    {
        $this->addNewMandate('foobar', $account, $id);
        $treeBuilder->addCreateMandateRequest('foobar', $account, $id)->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_delete_mandate($treeBuilder)
    {
        $this->deleteMandate('foobar');
        $treeBuilder->addDeleteMandateRequest('foobar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_accept_mandate($treeBuilder)
    {
        $this->acceptDigitalMandate('foobar');
        $treeBuilder->addAcceptDigitalMandateRequest('foobar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_reject_mandate($treeBuilder)
    {
        $this->rejectDigitalMandate('foobar');
        $treeBuilder->addRejectDigitalMandateRequest('foobar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_update_mandate($treeBuilder)
    {
        $this->updateMandate('foo', 'bar');
        $treeBuilder->addUpdateMandateRequest('foo', 'bar')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_add_payment($treeBuilder)
    {
        $amount = Money::SEK(100);
        $date = new \DateTime;
        $this->addPayment('foo', $amount, $date, 'ref', '10', 100);
        $treeBuilder->addIncomingPaymentRequest('foo', $amount, $date, 'ref', '10', 100)->shouldHaveBeenCalled();
    }

    function it_defaults_to_creating_one_time_payments($treeBuilder)
    {
        $amount = Money::SEK(100);
        $date = new \DateTime;
        $this->addPayment('foo', $amount, $date);
        $treeBuilder->addIncomingPaymentRequest('foo', $amount, $date, '', Intervals::INTERVAL_ONCE, 0)
            ->shouldHaveBeenCalled();
    }

    function it_creates_monthly_payments($treeBuilder)
    {
        $amount = Money::SEK(100);
        $date = new \DateTime;
        $this->addMonthlyPayment('foo', $amount, $date, 'ref');
        $treeBuilder->addIncomingPaymentRequest('foo', $amount, $date, 'ref', Intervals::INTERVAL_MONTHLY_ON_DATE, 0)
            ->shouldHaveBeenCalled();
    }

    function it_creates_immediate_payments($treeBuilder)
    {
        $amount = Money::SEK(100);
        $this->addImmediatePayment('foo', $amount, 'ref');
        $treeBuilder->addImmediateIncomingPaymentRequest('foo', $amount, 'ref')->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_add_outgoing_payment($treeBuilder)
    {
        $amount = Money::SEK(100);
        $date = new \DateTime;
        $this->addOutgoingPayment('foo', $amount, $date, 'ref', '10', 100);
        $treeBuilder->addOutgoingPaymentRequest('foo', $amount, $date, 'ref', '10', 100)->shouldHaveBeenCalled();
    }

    function it_creates_immediate_outgoing_payments($treeBuilder)
    {
        $amount = Money::SEK(100);
        $this->addImmediateOutgoingPayment('foo', $amount, 'ref');
        $treeBuilder->addImmediateOutgoingPaymentRequest('foo', $amount, 'ref')->shouldHaveBeenCalled();
    }

    function it_creates_payment_deletion_requests($treeBuilder)
    {
        $this->deletePayments('foo');
        $treeBuilder->addDeletePaymentRequest('foo')->shouldHaveBeenCalled();
    }
}
