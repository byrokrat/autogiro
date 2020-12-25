<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Intervals;
use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\ImmediateDate;
use byrokrat\autogiro\Tree\Text;
use byrokrat\autogiro\Tree\Node;
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
        $this->shouldHaveType(TreeBuilder::class);
    }

    function it_can_reset()
    {
        $this->addDeleteMandateRequest('payerNr');
        $this->reset();
        $this->buildTree()->shouldBeLike(new AutogiroFile(Node::AUTOGIRO_REQUEST_FILE));
    }

    function a_tree(string $sectionName, $bankgiro, $date, ...$nodes)
    {
        return new AutogiroFile(
            Node::AUTOGIRO_REQUEST_FILE,
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
            Node::OPENING,
            new Obj(0, $date->getWrappedObject(), Node::DATE),
            new Text(0, 'AUTOGIRO'),
            new Text(0, str_pad('', 44)),
            new Number(0, self::BCG_NR, Node::PAYEE_BGC_NUMBER),
            new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
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
                Node::MANDATE_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::CREATE_MANDATE_REQUEST,
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
                    new Number(0, 'payerNr', Node::PAYER_NUMBER),
                    new Obj(0, $account->getWrappedObject(), Node::ACCOUNT),
                    new Obj(0, $id->getWrappedObject(), Node::STATE_ID),
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
                Node::MANDATE_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::DELETE_MANDATE_REQUEST,
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
                    new Number(0, 'payerNr', Node::PAYER_NUMBER),
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
                Node::MANDATE_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::ACCEPT_DIGITAL_MANDATE_REQUEST,
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
                    new Number(0, 'payerNr', Node::PAYER_NUMBER),
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
                Node::MANDATE_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::REJECT_DIGITAL_MANDATE_REQUEST,
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
                    new Number(0, 'payerNr', Node::PAYER_NUMBER),
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

        $payeeBgNode = new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Node::MANDATE_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::UPDATE_MANDATE_REQUEST,
                    $payeeBgNode,
                    new Number(0, 'foo', Node::PAYER_NUMBER),
                    $payeeBgNode,
                    new Number(0, 'bar', Node::PAYER_NUMBER),
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
                Node::PAYMENT_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::INCOMING_PAYMENT_REQUEST,
                    new Obj(0, $date->getWrappedObject(), Node::DATE),
                    new Number(0, 'ival', Node::INTERVAL),
                    new Number(0, '1', Node::REPETITIONS),
                    new Text(0, ' '),
                    new Number(0, 'foobar', Node::PAYER_NUMBER),
                    new Obj(0, $amount, Node::AMOUNT),
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
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
                Node::PAYMENT_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::OUTGOING_PAYMENT_REQUEST,
                    new Obj(0, $date->getWrappedObject(), Node::DATE),
                    new Number(0, 'ival', Node::INTERVAL),
                    new Number(0, '1', Node::REPETITIONS),
                    new Text(0, ' '),
                    new Number(0, 'foobar', Node::PAYER_NUMBER),
                    new Obj(0, $amount, Node::AMOUNT),
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
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
                Node::PAYMENT_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::INCOMING_PAYMENT_REQUEST,
                    new ImmediateDate,
                    new Number(0, Intervals::INTERVAL_ONCE, Node::INTERVAL),
                    new Number(0, '0', Node::REPETITIONS),
                    new Text(0, ' '),
                    new Number(0, 'foobar', Node::PAYER_NUMBER),
                    new Obj(0, $amount, Node::AMOUNT),
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
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
                Node::PAYMENT_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::OUTGOING_PAYMENT_REQUEST,
                    new ImmediateDate,
                    new Number(0, Intervals::INTERVAL_ONCE, Node::INTERVAL),
                    new Number(0, '0', Node::REPETITIONS),
                    new Text(0, ' '),
                    new Number(0, 'foobar', Node::PAYER_NUMBER),
                    new Obj(0, $amount, Node::AMOUNT),
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
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
                Node::AMENDMENT_REQUEST_SECTION,
                $bankgiro,
                $date,
                new Record(
                    Node::AMENDMENT_REQUEST,
                    new Obj(0, $bankgiro->getWrappedObject(), Node::PAYEE_BANKGIRO),
                    new Number(0, 'foobar', Node::PAYER_NUMBER),
                    new Text(0, str_pad('', 52))
                )
            )
        );
    }
}
