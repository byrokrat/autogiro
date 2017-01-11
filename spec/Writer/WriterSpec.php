<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\Writer;
use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Writer\PrintingVisitor;
use byrokrat\autogiro\Writer\OutputInterface;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Tree\FileNode;
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

    function it_can_write_to_output($treeBuilder, $printer, $visitor, FileNode $tree, OutputInterface $output)
    {
        $treeBuilder->buildTree()->willReturn($tree)->shouldBeCalled();
        $tree->accept($visitor)->shouldBeCalled();
        $printer->setOutput($output)->shouldBeCalled();
        $tree->accept($printer)->shouldBeCalled();
        $this->writeTo($output);
    }

    function it_calls_tree_builder_on_reset($treeBuilder)
    {
        $this->reset();
        $treeBuilder->reset()->shouldHaveBeenCalled();
    }

    function it_calls_tree_builder_on_delete_mandate($treeBuilder)
    {
        $this->deleteMandate('foobar');
        $treeBuilder->addDeleteMandateRecord('foobar')->shouldHaveBeenCalled();
    }
}
