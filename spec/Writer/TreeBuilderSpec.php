<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Intervals;
use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\ImmediateDate;
use byrokrat\autogiro\Tree\Text;
use byrokrat\autogiro\Tree\Number;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Tree\Section;
use byrokrat\banking\AccountNumber;
use byrokrat\id\IdInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TreeBuilderSpec extends ObjectBehavior
{
    const BCG_NR = 'bgcNr';
    const BANKGIRO = 'bankgiro';
    const DATE = 'date';

    function let(AccountNumber $bankgiro, \DateTime $date)
    {
        $bankgiro->getNumber()->willReturn(self::BANKGIRO);
        $date->format('Ymd')->willReturn(self::DATE);
        $this->beConstructedWith(self::BCG_NR, $bankgiro, $date);
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
            new Obj(0, $date->getWrappedObject(), 'Date'),
            new Text(0, 'AUTOGIRO'),
            new Text(0, str_pad('', 44)),
            new Number(0, self::BCG_NR, 'PayeeBgcNumber'),
            new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
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
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
                    new Number(0, 'payerNr', 'PayerNumber'),
                    new Obj(0, $account->getWrappedObject(), 'Account'),
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
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
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
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
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
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
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

        $payeeBgNode = new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro');

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

    function it_builds_incoming_payment_trees($bankgiro, $date)
    {
        $amount = Money::SEK(100);

        $this->addIncomingPaymentRequest('foobar', $amount, $date, 'ref', 'ival', 1);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'PaymentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'IncomingPaymentRequest',
                    new Obj(0, $date->getWrappedObject(), 'Date'),
                    new Number(0, 'ival', 'Interval'),
                    new Number(0, '1', 'Repetitions'),
                    new Text(0, ' '),
                    new Number(0, 'foobar', 'PayerNumber'),
                    new Obj(0, $amount, 'Amount'),
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
                    new Text(0, '             ref'),
                    new Text(0, str_pad('', 11))
                )
            )
        );
    }

    function it_builds_outgoing_payment_trees($bankgiro, $date)
    {
        $amount = Money::SEK(100);

        $this->addOutgoingPaymentRequest('foobar', $amount, $date, 'ref', 'ival', 1);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'PaymentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'OutgoingPaymentRequest',
                    new Obj(0, $date->getWrappedObject(), 'Date'),
                    new Number(0, 'ival', 'Interval'),
                    new Number(0, '1', 'Repetitions'),
                    new Text(0, ' '),
                    new Number(0, 'foobar', 'PayerNumber'),
                    new Obj(0, $amount, 'Amount'),
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
                    new Text(0, '             ref'),
                    new Text(0, str_pad('', 11))
                )
            )
        );
    }

    function it_builds_immediate_incoming_payment_trees($bankgiro, $date)
    {
        $amount = Money::SEK(100);

        $this->addImmediateIncomingPaymentRequest('foobar', $amount, 'ref');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'PaymentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'IncomingPaymentRequest',
                    new ImmediateDate,
                    new Number(0, Intervals::INTERVAL_ONCE, 'Interval'),
                    new Number(0, '0', 'Repetitions'),
                    new Text(0, ' '),
                    new Number(0, 'foobar', 'PayerNumber'),
                    new Obj(0, $amount, 'Amount'),
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
                    new Text(0, '             ref'),
                    new Text(0, str_pad('', 11))
                )
            )
        );
    }

    function it_builds_immediate_outgoing_payment_trees($bankgiro, $date)
    {
        $amount = Money::SEK(100);

        $this->addImmediateOutgoingPaymentRequest('foobar', $amount, 'ref');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'PaymentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'OutgoingPaymentRequest',
                    new ImmediateDate,
                    new Number(0, Intervals::INTERVAL_ONCE, 'Interval'),
                    new Number(0, '0', 'Repetitions'),
                    new Text(0, ' '),
                    new Number(0, 'foobar', 'PayerNumber'),
                    new Obj(0, $amount, 'Amount'),
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
                    new Text(0, '             ref'),
                    new Text(0, str_pad('', 11))
                )
            )
        );
    }

    function it_builds_delete_payment_request_trees($bankgiro, $date)
    {
        $this->addDeletePaymentRequest('foobar');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                'AmendmentRequestSection',
                $bankgiro,
                $date,
                new Record(
                    'AmendmentRequest',
                    new Obj(0, $bankgiro->getWrappedObject(), 'PayeeBankgiro'),
                    new Number(0, 'foobar', 'PayerNumber'),
                    new Text(0, str_pad('', 52))
                )
            )
        );
    }
}
