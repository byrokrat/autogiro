<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\BankgiroProcessor;
use byrokrat\autogiro\Exception;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\RequestMandateCreationNode;
use byrokrat\autogiro\Tree\RequestMandateAcceptanceNode;
use byrokrat\autogiro\Tree\RequestMandateRejectionNode;
use byrokrat\autogiro\Tree\RequestMandateUpdateNode;
use byrokrat\autogiro\Tree\RequestMandateDeletionNode;
use byrokrat\autogiro\Tree\MandateResponseNode;
use byrokrat\autogiro\Tree\BankgiroNode;
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

    function it_can_reset_errors(OpeningNode $opening, MandateResponseNode $node, $bg1, $bg2)
    {
        $this->hasErrors()->shouldEqual(false);
        $this->getErrors()->shouldHaveCount(0);

        $this->visitOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->visitMandateResponseNode($this->a_node_with_bankgiro($node, $bg2));

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);

        $this->resetErrors();

        $this->hasErrors()->shouldEqual(false);
        $this->getErrors()->shouldHaveCount(0);
    }

    function it_fails_on_wrong_request_mandate_creation_bg(OpeningNode $opening, RequestMandateCreationNode $node, $bg1, $bg2)
    {
        $this->visitOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->visitRequestMandateCreationNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_rejection_bg(OpeningNode $opening, RequestMandateRejectionNode $node, $bg1, $bg2)
    {
        $this->visitOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->visitRequestMandateRejectionNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_acceptance_bg(OpeningNode $opening, RequestMandateAcceptanceNode $node, $bg1, $bg2)
    {
        $this->visitOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->visitRequestMandateAcceptanceNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_update_bg(OpeningNode $opening, RequestMandateUpdateNode $node, $bg1, $bg2)
    {
        $node = $this->a_node_with_bankgiro($node, $bg2);
        $node->getChild('new_bankgiro')->willReturn($bg2);

        $this->visitOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->visitRequestMandateUpdateNode($node);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_update_bg_duplicate(OpeningNode $opening, RequestMandateUpdateNode $node, $bg1, $bg2)
    {
        $node = $this->a_node_with_bankgiro($node, $bg1);
        $node->getChild('new_bankgiro')->willReturn($bg2);

        $this->visitOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->visitRequestMandateUpdateNode($node);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_request_mandate_deletion_bg(OpeningNode $opening, RequestMandateDeletionNode $node, $bg1, $bg2)
    {
        $this->visitOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->visitRequestMandateDeletionNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_mandate_response_bg(OpeningNode $opening, MandateResponseNode $node, $bg1, $bg2)
    {
        $this->visitOpeningNode($this->a_node_with_bankgiro($opening, $bg1));
        $this->visitMandateResponseNode($this->a_node_with_bankgiro($node, $bg2));
        $this->getErrors()->shouldHaveCount(1);
    }
}
