<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Writer\IntervalFormatter;
use byrokrat\autogiro\Writer\RepititionsFormatter;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Date;
use byrokrat\autogiro\Tree\ImmediateDate;
use byrokrat\autogiro\Tree\Text;
use byrokrat\autogiro\Tree\PayeeBankgiro;
use byrokrat\autogiro\Tree\Account;
use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Tree\Number;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\autogiro\Tree\Amount;
use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Tree\Section;
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
        $this->buildTree()->shouldBeLike(new AutogiroFile('AutogiroRequestFile'));
    }

    function a_tree(string $sectionName, $bankgiro, $date, ...$nodes)
    {
        return new AutogiroFile(
            'AutogiroRequestFile',
            new Section(
                $sectionName,
                $this->an_opening_record_node($bankgiro, $date),
                ...$nodes
            )
        );
    }

    function an_opening_record_node($bankgiro, $date)
    {
        return new Record(
            'Opening',
            Date::fromDate($date->getWrappedObject()),
            new Text(0, 'AUTOGIRO'),
            new Text(0, str_pad('', 44)),
            new Number(0, self::BCG_NR, 'PayeeBgcNumber'),
            PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
            new Text(0, '  ')
        );
    }

    function it_builds_create_mandate_trees($bankgiro, $date, AccountNumber $account, IdInterface $id)
    {
        $account->getNumber()->willReturn('account_number');
        $id->__tostring()->willReturn('id_number');

        $this->addCreateMandateRequest('payerNr', $account, $id);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'MandateRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'CreateMandateRequest',
                    PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                    new Number(0, 'payerNr', 'PayerNumber'),
                    Account::fromAccount($account->getWrappedObject()),
                    new Obj(0, $id->getWrappedObject(), 'StateId'),
                    new Text(0, str_pad('', 24))
                )
            )
        );
    }

    function it_builds_delete_mandate_trees($bankgiro, $date)
    {
        $this->addDeleteMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'MandateRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'DeleteMandateRequest',
                    PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                    new Number(0, 'payerNr', 'PayerNumber'),
                    new Text(0, str_pad('', 52))
                )
            )
        );
    }

    function it_builds_accept_mandate_trees($bankgiro, $date)
    {
        $this->addAcceptDigitalMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'MandateRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'AcceptDigitalMandateRequest',
                    PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                    new Number(0, 'payerNr', 'PayerNumber'),
                    new Text(0, str_pad('', 52))
                )
            )
        );
    }

    function it_builds_reject_mandate_trees($bankgiro, $date)
    {
        $this->addRejectDigitalMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'MandateRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'RejectDigitalMandateRequest',
                    PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                    new Number(0, 'payerNr', 'PayerNumber'),
                    new Text(0, str_pad('', 48)),
                    new Text(0, 'AV'),
                    new Text(0, '  ')
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
                'MandateRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'UpdateMandateRequest',
                    $payeeBgNode,
                    new Number(0, 'foo', 'PayerNumber'),
                    $payeeBgNode,
                    new Number(0, 'bar', 'PayerNumber'),
                    new Text(0, str_pad('', 26))
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
                'PaymentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'IncomingPaymentRequest',
                    Date::fromDate($date->getWrappedObject()),
                    new Interval(0, 'formatted_interval'),
                    new Text(0, 'formatted_repititions', 'Repetitions'),
                    new Text(0, ' '),
                    new Number(0, 'foobar', 'PayerNumber'),
                    Amount::fromAmount($amount->getWrappedObject()),
                    PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                    new Text(0, '             ref'),
                    new Text(0, str_pad('', 11))
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
                'PaymentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'OutgoingPaymentRequest',
                    Date::fromDate($date->getWrappedObject()),
                    new Interval(0, 'formatted_interval'),
                    new Text(0, 'formatted_repititions', 'Repetitions'),
                    new Text(0, ' '),
                    new Number(0, 'foobar', 'PayerNumber'),
                    Amount::fromAmount($amount->getWrappedObject()),
                    PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                    new Text(0, '             ref'),
                    new Text(0, str_pad('', 11))
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
                'PaymentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'IncomingPaymentRequest',
                    new ImmediateDate,
                    new Interval(0, '0'),
                    new Text(0, '   ', 'Repetitions'),
                    new Text(0, ' '),
                    new Number(0, 'foobar', 'PayerNumber'),
                    Amount::fromAmount($amount->getWrappedObject()),
                    PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                    new Text(0, '             ref'),
                    new Text(0, str_pad('', 11))
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
                'PaymentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'OutgoingPaymentRequest',
                    new ImmediateDate,
                    new Interval(0, '0'),
                    new Text(0, '   ', 'Repetitions'),
                    new Text(0, ' '),
                    new Number(0, 'foobar', 'PayerNumber'),
                    Amount::fromAmount($amount->getWrappedObject()),
                    PayeeBankgiro::fromBankgiro($bankgiro->getWrappedObject()),
                    new Text(0, '             ref'),
                    new Text(0, str_pad('', 11))
                )
            )
        );
    }
}
