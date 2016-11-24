<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\BankgiroProcessor;
use byrokrat\autogiro\Exception;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\Record\Request\CreateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\AcceptMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\RejectMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\UpdateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\DeleteMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Response\MandateResponseNode;
use byrokrat\autogiro\Tree\Account\BankgiroNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BankgiroProcessorSpec extends ObjectBehavior
{
    function let(BankgiroNode $bg1, BankgiroNode $bg2)
    {
        $bg1->getValue()->willReturn('1');
        $bg2->getValue()->willReturn('2');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BankgiroProcessor::CLASS);
    }

    function a_node_with_bankgiro($node, $bankgiro)
    {
        $node->getChild('bankgiro')->willReturn($bankgiro);
        $node->getLineNr()->willReturn(1);
        $node->getType()->willReturn('mock');

        return $node;
    }

    function it_fails_on_wrong_request_mandate_creation_bg(OpeningNode $opening, CreateMandateRequestNode $node, $bg1, $bg2)
    {
        $this->beforeOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->afterCreateMandateRequestNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_rejection_bg(OpeningNode $opening, RejectMandateRequestNode $node, $bg1, $bg2)
    {
        $this->beforeOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->afterRejectMandateRequestNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_acceptance_bg(OpeningNode $opening, AcceptMandateRequestNode $node, $bg1, $bg2)
    {
        $this->beforeOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->afterAcceptMandateRequestNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_update_bg(OpeningNode $opening, UpdateMandateRequestNode $node, $bg1, $bg2)
    {
        $node = $this->a_node_with_bankgiro($node, $bg2);
        $node->getChild('new_bankgiro')->willReturn($bg2);

        $this->beforeOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->afterUpdateMandateRequestNode($node);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_update_bg_duplicate(OpeningNode $opening, UpdateMandateRequestNode $node, $bg1, $bg2)
    {
        $node = $this->a_node_with_bankgiro($node, $bg1);
        $node->getChild('new_bankgiro')->willReturn($bg2);

        $this->beforeOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->afterUpdateMandateRequestNode($node);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_deletion_bg(OpeningNode $opening, DeleteMandateRequestNode $node, $bg1, $bg2)
    {
        $this->beforeOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->afterDeleteMandateRequestNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_mandate_response_bg(OpeningNode $opening, MandateResponseNode $node, $bg1, $bg2)
    {
        $this->beforeOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->afterMandateResponseNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }
}
