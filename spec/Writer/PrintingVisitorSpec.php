<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\PrintingVisitor;
use byrokrat\autogiro\Writer\Output;
use byrokrat\autogiro\Exception\RuntimeException;
use byrokrat\autogiro\Exception\LogicException;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\BgcNumberNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
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

    function it_prints_dates(DateNode $node, $output)
    {
        $node->hasAttribute('date')->willReturn(true);
        $node->getAttribute('date')->willReturn(new \DateTime('2017-01-10'));
        $this->beforeDateNode($node);
        $output->write('20170110')->shouldHaveBeenCalled();
    }

    function it_fails_on_missing_date(DateNode $node)
    {
        $node->hasAttribute('date')->willReturn(false);
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeDateNode($node);
    }

    function it_fails_on_unvalid_date(DateNode $node)
    {
        $node->hasAttribute('date')->willReturn(true);
        $node->getAttribute('date')->willReturn('not-an-object');
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeDateNode($node);
    }

    function it_prints_immediate_dates($output)
    {
        $this->beforeImmediateDateNode();
        $output->write(Argument::is('GENAST  '))->shouldHaveBeenCalled();
    }

    function it_print_text_nodes(TextNode $node, $output)
    {
        $node->getValue()->willReturn('foobar');
        $this->beforeTextNode($node);
        $output->write('foobar')->shouldHaveBeenCalled();
    }

    function it_prints_payee_bgc_numbers(BgcNumberNode $node, $output)
    {
        $node->getValue()->willReturn('111');
        $this->beforeBgcNumberNode($node);
        $output->write(Argument::is('000111'))->shouldHaveBeenCalled();
    }

    function it_prints_payee_bankgiro_numbers(BankgiroNode $node, AccountNumber $account, $output)
    {
        $account->getSerialNumber()->willReturn('1234567');
        $account->getCheckDigit()->willReturn('8');
        $node->hasAttribute('account')->willReturn(true);
        $node->getAttribute('account')->willReturn($account);
        $this->beforeBankgiroNode($node);
        $output->write(Argument::is('0012345678'))->shouldHaveBeenCalled();
    }

    function it_fails_on_missing_payee_bankgiro_numbers(BankgiroNode $node)
    {
        $node->hasAttribute('account')->willReturn(false);
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeBankgiroNode($node);
    }

    function it_fails_on_unvalid_payee_bankgiro_numbers(BankgiroNode $node)
    {
        $node->hasAttribute('account')->willReturn(true);
        $node->getAttribute('account')->willReturn('not-an-object');
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeBankgiroNode($node);
    }

    function it_prints_payer_numbers(PayerNumberNode $node, $output)
    {
        $node->getValue()->willReturn('1234567890');
        $this->beforePayerNumberNode($node);
        $output->write(Argument::is('0000001234567890'))->shouldHaveBeenCalled();
    }

    function it_prints_account_numbers(AccountNode $node, AccountNumber $account, $output)
    {
        $account->getClearingNumber()->willReturn('1111');
        $account->getSerialNumber()->willReturn('1234567');
        $account->getCheckDigit()->willReturn('8');
        $node->hasAttribute('account')->willReturn(true);
        $node->getAttribute('account')->willReturn($account);
        $this->beforeAccountNode($node);
        $output->write('1111000012345678')->shouldHaveBeenCalled();
    }

    function it_fails_on_missing_account_numbers(AccountNode $node)
    {
        $node->hasAttribute('account')->willReturn(false);
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeAccountNode($node);
    }

    function it_fails_on_invalid_account_numbers(AccountNode $node)
    {
        $node->hasAttribute('account')->willReturn(true);
        $node->getAttribute('account')->willReturn('not-an-object');
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeAccountNode($node);
    }

    function it_prints_intervals(IntervalNode $node, $output)
    {
        $node->getValue()->willReturn('9');
        $this->beforeIntervalNode($node);
        $output->write('9')->shouldHaveBeenCalled();
    }

    function it_prints_repetitions(RepetitionsNode $node, $output)
    {
        $node->getValue()->willReturn('123');
        $this->beforeRepetitionsNode($node);
        $output->write('123')->shouldHaveBeenCalled();
    }

    function it_prints_amounts(AmountNode $node, $output)
    {
        $node->hasAttribute('amount')->willReturn(true);
        $node->getAttribute('amount')->willReturn(new SEK('12345678.90'));
        $this->beforeAmountNode($node);
        $output->write(Argument::is('001234567890'))->shouldHaveBeenCalled();
    }

    function it_fails_on_to_large_amounts(AmountNode $node)
    {
        $node->hasAttribute('amount')->willReturn(true);
        $node->getAttribute('amount')->willReturn(new SEK('10000000000.00'));
        $this->shouldThrow(RuntimeException::CLASS)->duringBeforeAmountNode($node);
    }

    function it_fails_on_to_small_amounts(AmountNode $node)
    {
        $node->hasAttribute('amount')->willReturn(true);
        $node->getAttribute('amount')->willReturn(new SEK('-10000000000.00'));
        $this->shouldThrow(RuntimeException::CLASS)->duringBeforeAmountNode($node);
    }

    function it_fails_on_missing_amounts(AmountNode $node)
    {
        $node->hasAttribute('amount')->willReturn(false);
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeAmountNode($node);
    }

    function it_fails_on_invalid_amounts(AmountNode $node)
    {
        $node->hasAttribute('amount')->willReturn(true);
        $node->getAttribute('amount')->willReturn('not-an-object');
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeAmountNode($node);
    }

    function it_prints_personal_ids(IdNode $node, PersonalId $id, $output)
    {
        $id->format('Ymdsk')->willReturn('201701101111');
        $node->hasAttribute('id')->willReturn(true);
        $node->getAttribute('id')->willReturn($id);
        $this->beforeIdNode($node);
        $output->write('201701101111')->shouldHaveBeenCalled();
    }

    function it_prints_organization_ids(IdNode $node, OrganizationId $id, $output)
    {
        $id->format('00Ssk')->willReturn('001234561111');
        $node->hasAttribute('id')->willReturn(true);
        $node->getAttribute('id')->willReturn($id);
        $this->beforeIdNode($node);
        $output->write(Argument::is('001234561111'))->shouldHaveBeenCalled();
    }

    function it_fails_on_missing_ids(IdNode $node)
    {
        $node->hasAttribute('id')->willReturn(false);
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeIdNode($node);
    }

    function it_fails_on_invalid_ids(IdNode $node)
    {
        $node->hasAttribute('id')->willReturn(true);
        $node->getAttribute('id')->willReturn('not-an-object');
        $node->getType()->willReturn('');
        $this->shouldThrow(LogicException::CLASS)->duringBeforeIdNode($node);
    }

    function it_prints_transaction_code_before_opening($output)
    {
        $this->beforeRequestOpening();
        $output->write('01')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_opening($output)
    {
        $this->afterRequestOpening();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_create_mandate($output)
    {
        $this->beforeCreateMandateRequest();
        $output->write('04')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_create_mandate($output)
    {
        $this->afterCreateMandateRequest();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_delete_mandate($output)
    {
        $this->beforeDeleteMandateRequest();
        $output->write('03')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_delete_mandate($output)
    {
        $this->afterDeleteMandateRequest();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_accept_mandate($output)
    {
        $this->beforeAcceptDigitalMandateRequest();
        $output->write('04')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_accept_mandate($output)
    {
        $this->afterAcceptDigitalMandateRequest();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_reject_mandate($output)
    {
        $this->beforeRejectDigitalMandateRequest();
        $output->write('04')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_reject_mandate($output)
    {
        $this->afterRejectDigitalMandateRequest();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_update_mandate($output)
    {
        $this->beforeUpdateMandateRequest();
        $output->write('05')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_update_mandate($output)
    {
        $this->afterUpdateMandateRequest();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_incoming_payment($output)
    {
        $this->beforeIncomingPaymentRequest();
        $output->write('82')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_incoming_payment($output)
    {
        $this->afterIncomingPaymentRequest();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }

    function it_prints_transaction_code_before_outgoing_payment($output)
    {
        $this->beforeOutgoingPaymentRequest();
        $output->write('32')->shouldHaveBeenCalled();
    }

    function it_prints_new_line_after_outgoing_payment($output)
    {
        $this->afterOutgoingPaymentRequest();
        $output->write(PrintingVisitor::EOL)->shouldHaveBeenCalled();
    }
}
