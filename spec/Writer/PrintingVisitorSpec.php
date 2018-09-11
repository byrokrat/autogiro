<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\PrintingVisitor;
use byrokrat\autogiro\Writer\Output;
use byrokrat\autogiro\Exception\RuntimeException;
use byrokrat\autogiro\Exception\LogicException;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\amount\Currency\SEK;
use byrokrat\banking\AccountNumber;
use byrokrat\id\PersonalId;
use byrokrat\id\OrganizationId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PrintingVisitorSpec extends ObjectBehavior
{
    function let(Output $output)
    {
        $this->setOutput($output);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PrintingVisitor::CLASS);
    }

    function it_is_a_visitor()
    {
        $this->shouldHaveType(Visitor::CLASS);
    }

    function it_prints_dates(Node $node, $output)
    {
        $node->getValue()->willReturn(new \DateTime('2017-01-10'));
        $this->beforeDate($node);
        $output->write('20170110')->shouldHaveBeenCalled();
    }

    function it_fails_on_unvalid_date(Node $node, $output)
    {
        $node->getValue()->willReturn('not-an-object');
        $this->beforeDate($node);
        $output->write(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_prints_immediate_dates($output)
    {
        $this->beforeImmediateDate();
        $output->write(Argument::is('GENAST  '))->shouldHaveBeenCalled();
    }

    function it_print_text_nodes(Node $node, $output)
    {
        $node->getValue()->willReturn('foobar');
        $this->beforeText($node);
        $output->write('foobar')->shouldHaveBeenCalled();
    }

    function it_prints_payer_numbers(Node $node, $output)
    {
        $node->getValue()->willReturn('1234567890');
        $this->beforePayerNumber($node);
        $output->write(Argument::is('0000001234567890'))->shouldHaveBeenCalled();
    }

    function it_prints_payee_bgc_numbers(Node $node, $output)
    {
        $node->getValue()->willReturn('111');
        $this->beforePayeeBgcNumber($node);
        $output->write(Argument::is('000111'))->shouldHaveBeenCalled();
    }

    function it_prints_payee_bankgiro_numbers(Node $node, AccountNumber $account, $output)
    {
        $account->getSerialNumber()->willReturn('1234567');
        $account->getCheckDigit()->willReturn('8');
        $node->getValue()->willReturn($account);
        $this->beforePayeeBankgiro($node);
        $output->write(Argument::is('0012345678'))->shouldHaveBeenCalled();
    }

    function it_fails_on_unvalid_payee_bankgiro_numbers(Node $node, $output)
    {
        $node->getValue()->willReturn('not-an-object');
        $this->beforePayeeBankgiro($node);
        $output->write(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_prints_account_numbers(Node $node, AccountNumber $account, $output)
    {
        $account->getClearingNumber()->willReturn('1111');
        $account->getSerialNumber()->willReturn('1234567');
        $account->getCheckDigit()->willReturn('8');
        $node->getValue()->willReturn($account);
        $this->beforeAccount($node);
        $output->write('1111000012345678')->shouldHaveBeenCalled();
    }

    function it_fails_on_invalid_account_numbers(Node $node, $output)
    {
        $node->getValue()->willReturn('not-an-object');
        $output->write(Argument::any())->shouldNotBeCalled();
        $this->beforeAccount($node);
    }

    function it_prints_intervals(Node $node, $output)
    {
        $node->getValue()->willReturn('8');
        $this->beforeInterval($node);
        $output->write('8')->shouldHaveBeenCalled();
    }

    function it_fails_on_invalid_intervals(Node $node, $output)
    {
        $node->getValue()->willReturn('9');
        $this->shouldThrow(RuntimeException::CLASS)->duringBeforeInterval($node);
        $output->write(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_prints_repititions(Node $node, $output)
    {
        $node->getValue()->willReturn('123');
        $this->beforeRepetitions($node);
        $output->write('123')->shouldHaveBeenCalled();
    }

    function it_prints_cero_repititions(Node $node, $output)
    {
        $node->getValue()->willReturn('0');
        $this->beforeRepetitions($node);
        $output->write('   ')->shouldHaveBeenCalled();
    }

    function it_pads_repititions(Node $node, $output)
    {
        $node->getValue()->willReturn('1');
        $this->beforeRepetitions($node);
        $output->write('001')->shouldHaveBeenCalled();
    }

    function it_fails_on_invalid_repititions(Node $node, $output)
    {
        $node->getValue()->willReturn('1000');
        $this->shouldThrow(RuntimeException::CLASS)->duringBeforeRepetitions($node);
        $output->write(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_prints_amounts(Node $node, $output)
    {
        $node->getValue()->willReturn(new SEK('12345678.90'));
        $this->beforeAmount($node);
        $output->write(Argument::is('001234567890'))->shouldHaveBeenCalled();
    }

    function it_fails_on_to_large_amounts(Node $node)
    {
        $node->getValue()->willReturn(new SEK('10000000000.00'));
        $this->shouldThrow(RuntimeException::CLASS)->duringBeforeAmount($node);
    }

    function it_fails_on_to_small_amounts(Node $node)
    {
        $node->getValue()->willReturn(new SEK('-10000000000.00'));
        $this->shouldThrow(RuntimeException::CLASS)->duringBeforeAmount($node);
    }

    function it_ignores_missing_amounts(Node $node, $output)
    {
        $node->getValue()->willReturn('this is not an amount');
        $output->write(Argument::any())->shouldNotBeCalled();
        $this->beforeAmount($node);
    }

    function it_prints_personal_ids(Node $node, PersonalId $id, $output)
    {
        $id->format('Ymdsk')->willReturn('201701101111');
        $node->getValue()->willReturn($id);
        $this->beforeStateId($node);
        $output->write('201701101111')->shouldHaveBeenCalled();
    }

    function it_prints_organization_ids(Node $node, OrganizationId $id, $output)
    {
        $id->format('00Ssk')->willReturn('001234561111');
        $node->getValue()->willReturn($id);
        $this->beforeStateId($node);
        $output->write(Argument::is('001234561111'))->shouldHaveBeenCalled();
    }

    function it_ignores_invalid_ids(Node $node, $output)
    {
        $node->getValue()->willReturn(null);
        $this->beforeStateId($node);
        $output->write(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_prints_transaction_code_before_opening($output)
    {
        $this->beforeOpening();
        $output->write('01')->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_create_mandate($output)
    {
        $this->beforeCreateMandateRequest();
        $output->write('04')->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_delete_mandate($output)
    {
        $this->beforeDeleteMandateRequest();
        $output->write('03')->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_accept_mandate($output)
    {
        $this->beforeAcceptDigitalMandateRequest();
        $output->write('04')->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_reject_mandate($output)
    {
        $this->beforeRejectDigitalMandateRequest();
        $output->write('04')->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_update_mandate($output)
    {
        $this->beforeUpdateMandateRequest();
        $output->write('05')->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_incoming_payment($output)
    {
        $this->beforeIncomingPaymentRequest();
        $output->write('82')->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_outgoing_payment($output)
    {
        $this->beforeOutgoingPaymentRequest();
        $output->write('32')->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_payment_deletion($output)
    {
        $this->beforeAmendmentRequest();
        $output->write('23')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_record($output)
    {
        $this->afterRecord();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }
}
