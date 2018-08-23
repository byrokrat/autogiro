<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Writer\IntervalFormatter;
use byrokrat\autogiro\Writer\RepititionsFormatter;
use byrokrat\autogiro\Layouts;
use byrokrat\autogiro\Tree\Request\RequestOpening;
use byrokrat\autogiro\Tree\Request\AcceptDigitalMandateRequest;
use byrokrat\autogiro\Tree\Request\CreateMandateRequest;
use byrokrat\autogiro\Tree\Request\DeleteMandateRequest;
use byrokrat\autogiro\Tree\Request\RejectDigitalMandateRequest;
use byrokrat\autogiro\Tree\Request\UpdateMandateRequest;
use byrokrat\autogiro\Tree\Request\IncomingPaymentRequest;
use byrokrat\autogiro\Tree\Request\OutgoingPaymentRequest;
use byrokrat\autogiro\Tree\Request\MandateRequestSection;
use byrokrat\autogiro\Tree\Request\PaymentRequestSection;
use byrokrat\autogiro\Tree\Request\AmendmentRequestSection;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Date;
use byrokrat\autogiro\Tree\ImmediateDate;
use byrokrat\autogiro\Tree\Text;
use byrokrat\autogiro\Tree\PayeeBgcNumber;
use byrokrat\autogiro\Tree\PayeeBankgiro;
use byrokrat\autogiro\Tree\PayerNumber;
use byrokrat\autogiro\Tree\Account;
use byrokrat\autogiro\Tree\StateId;
use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Tree\Repetitions;
use byrokrat\autogiro\Tree\Amount;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Bankgiro;
use byrokrat\id\IdInterface;
use byrokrat\amount\Currency\SEK;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TreeBuilderSpec extends ObjectBehavior
{
    const BCG_NR = 'bgcNr';
    const BANKGIRO = 'bankgiro';
    const DATE = 'date';

    function let(
        Bankgiro $bankgiro,
        \DateTime $date,
        IntervalFormatter $intervalFormatter,
        RepititionsFormatter $repsFormatter
    ) {
        $bankgiro->getNumber()->willReturn(self::BANKGIRO);
        $date->format('Ymd')->willReturn(self::DATE);
        $this->beConstructedWith(self::BCG_NR, $bankgiro, $date, $intervalFormatter, $repsFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TreeBuilder::CLASS);
    }

    function it_can_reset()
    {
        $this->addDeleteMandateRequest('payerNr');
        $this->reset();
        $this->buildTree()->shouldBeLike(new AutogiroFile(Layouts::LAYOUT_REQUEST));
    }

    function a_tree(string $sectionClass, $bankgiro, $date, ...$nodes)
    {
        return new AutogiroFile(
            Layouts::LAYOUT_REQUEST,
            new $sectionClass(
                $this->an_opening_record_node($bankgiro, $date),
                ...$nodes
            )
        );
    }

    function an_opening_record_node($bankgiro, $date)
    {
        return new RequestOpening(
            0,
            [
                'date' => Date::fromDate($date->getWrappedObject()),
                'autogiro_txt' => new Text(0, 'AUTOGIRO'),
                'space' => new Text(0, str_pad('', 44)),
                'payee_bgc_number' => new PayeeBgcNumber(0, self::BCG_NR),
                'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                'end' => new Text(0, '  ')
            ]
        );
    }

    function it_builds_create_mandate_trees($bankgiro, $date, AccountNumber $account, IdInterface $id)
    {
        $account->getNumber()->willReturn('account_number');
        $id->__tostring()->willReturn('id_number');

        $this->addCreateMandateRequest('payerNr', $account, $id);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                MandateRequestSection::CLASS,
                $bankgiro,
                $date,
                new CreateMandateRequest(
                    0,
                    [
                        'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                        'payer_number' => new PayerNumber(0, 'payerNr'),
                        'account' => Account::fromAccount($account->getWrappedObject()),
                        'id' => StateId::fromId($id->getWrappedObject()),
                        'end' => new Text(0, str_pad('', 24))
                    ]
                )
            )
        );
    }

    function it_builds_delete_mandate_trees($bankgiro, $date)
    {
        $this->addDeleteMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                MandateRequestSection::CLASS,
                $bankgiro,
                $date,
                new DeleteMandateRequest(
                    0,
                    [
                        'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                        'payer_number' => new PayerNumber(0, 'payerNr'),
                        'end' => new Text(0, str_pad('', 52))
                    ]
                )
            )
        );
    }

    function it_builds_accept_mandate_trees($bankgiro, $date)
    {
        $this->addAcceptDigitalMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                MandateRequestSection::CLASS,
                $bankgiro,
                $date,
                new AcceptDigitalMandateRequest(
                    0,
                    [
                        'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                        'payer_number' => new PayerNumber(0, 'payerNr'),
                        'end' => new Text(0, str_pad('', 52))
                    ]
                )
            )
        );
    }

    function it_builds_reject_mandate_trees($bankgiro, $date)
    {
        $this->addRejectDigitalMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                MandateRequestSection::CLASS,
                $bankgiro,
                $date,
                new RejectDigitalMandateRequest(
                    0,
                    [
                        'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                        'payer_number' => new PayerNumber(0, 'payerNr'),
                        'space' => new Text(0, str_pad('', 48)),
                        'reject' => new Text(0, 'AV'),
                        'end' => new Text(0, '  ')
                    ]
                )
            )
        );
    }

    function it_builds_update_mandate_trees($bankgiro, $date)
    {
        $this->addUpdateMandateRequest('foo', 'bar');

        $payeeBgNode = PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject());

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                MandateRequestSection::CLASS,
                $bankgiro,
                $date,
                new UpdateMandateRequest(
                    0,
                    [
                        'payee_bankgiro' => $payeeBgNode,
                        'payer_number' => new PayerNumber(0, 'foo'),
                        'new_payee_bankgiro' => $payeeBgNode,
                        'new_payer_number' => new PayerNumber(0, 'bar'),
                        'end' => new Text(0, str_pad('', 26))
                    ]
                )
            )
        );
    }

    function it_builds_incoming_payment_trees(SEK $amount, $bankgiro, $date, $intervalFormatter, $repsFormatter)
    {
        $intervalFormatter->format(0)->shouldBeCalled()->willReturn('formatted_interval');
        $repsFormatter->format(1)->shouldBeCalled()->willReturn('formatted_repititions');
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addIncomingPaymentRequest('foobar', $amount, $date, 'ref', 0, 1);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                PaymentRequestSection::CLASS,
                $bankgiro,
                $date,
                new IncomingPaymentRequest(
                    0,
                    [
                        'date' => Date::fromDate($date->getWrappedObject()),
                        'interval' => new Interval(0, 'formatted_interval'),
                        'repetitions' => new Repetitions(0, 'formatted_repititions'),
                        'space' => new Text(0, ' '),
                        'payer_number' => new PayerNumber(0, 'foobar'),
                        'amount' => Amount::fromAmount($amount->getWrappedObject()),
                        'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                        'reference' => new Text(0, '             ref', '/^.{16}$/'),
                        'end' => new Text(0, str_pad('', 11))
                    ]
                )
            )
        );
    }

    function it_builds_outgoing_payment_trees(SEK $amount, $bankgiro, $date, $intervalFormatter, $repsFormatter)
    {
        $intervalFormatter->format(0)->shouldBeCalled()->willReturn('formatted_interval');
        $repsFormatter->format(1)->shouldBeCalled()->willReturn('formatted_repititions');
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addOutgoingPaymentRequest('foobar', $amount, $date, 'ref', 0, 1);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                PaymentRequestSection::CLASS,
                $bankgiro,
                $date,
                new OutgoingPaymentRequest(
                    0,
                    [
                        'date' => Date::fromDate($date->getWrappedObject()),
                        'interval' => new Interval(0, 'formatted_interval'),
                        'repetitions' => new Repetitions(0, 'formatted_repititions'),
                        'space' => new Text(0, ' '),
                        'payer_number' => new PayerNumber(0, 'foobar'),
                        'amount' => Amount::fromAmount($amount->getWrappedObject()),
                        'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                        'reference' => new Text(0, '             ref', '/^.{16}$/'),
                        'end' => new Text(0, str_pad('', 11))
                    ]
                )
            )
        );
    }

    function it_builds_immediate_incoming_payment_trees(SEK $amount, $bankgiro, $date)
    {
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addImmediateIncomingPaymentRequest('foobar', $amount, 'ref');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                PaymentRequestSection::CLASS,
                $bankgiro,
                $date,
                new IncomingPaymentRequest(
                    0,
                    [
                        'date' => new ImmediateDate,
                        'interval' => new Interval(0, '0'),
                        'repetitions' => new Repetitions(0, '   '),
                        'space' => new Text(0, ' '),
                        'payer_number' => new PayerNumber(0, 'foobar'),
                        'amount' => Amount::fromAmount($amount->getWrappedObject()),
                        'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                        'reference' => new Text(0, '             ref', '/^.{16}$/'),
                        'end' => new Text(0, str_pad('', 11))
                    ]
                )
            )
        );
    }

    function it_builds_immediate_outgoing_payment_trees(SEK $amount, $bankgiro, $date)
    {
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addImmediateOutgoingPaymentRequest('foobar', $amount, 'ref');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                PaymentRequestSection::CLASS,
                $bankgiro,
                $date,
                new OutgoingPaymentRequest(
                    0,
                    [
                        'date' => new ImmediateDate,
                        'interval' => new Interval(0, '0'),
                        'repetitions' => new Repetitions(0, '   '),
                        'space' => new Text(0, ' '),
                        'payer_number' => new PayerNumber(0, 'foobar'),
                        'amount' => Amount::fromAmount($amount->getWrappedObject()),
                        'payee_bankgiro' => PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                        'reference' => new Text(0, '             ref', '/^.{16}$/'),
                        'end' => new Text(0, str_pad('', 11))
                    ]
                )
            )
        );
    }
}
