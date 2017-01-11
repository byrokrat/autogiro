<?php
/**
 * This file is part of byrokrat\autogiro\Writer.
 *
 * byrokrat\autogiro\Writer is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat\autogiro\Writer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat\autogiro\Writer. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Visitor\Visitor;

/**
 * Facade for creating autogiro request files
 */
class Writer
{
    /**
     * @var TreeBuilder Helper used when building trees
     */
    private $treeBuilder;

    /**
     * @var PrintingVisitor Helper used when generating content
     */
    private $printer;

    /**
     * @var Visitor Helper used to validate and process tree
     */
    private $visitor;

    public function __construct(TreeBuilder $treeBuilder, PrintingVisitor $printer, Visitor $visitor)
    {
        $this->treeBuilder = $treeBuilder;
        $this->printer = $printer;
        $this->visitor = $visitor;
    }

    /**
     * Build request file and write content to $output
     */
    public function writeTo(OutputInterface $output)
    {
        $tree = $this->treeBuilder->buildTree();
        $tree->accept($this->visitor);
        $this->printer->setOutput($output);
        $tree->accept($this->printer);
    }

    /**
     * Reset internal buidld queue
     */
    public function reset()
    {
        $this->treeBuilder->reset();
    }

    /**
     * Add a delete mandate request to the build queue
     */
    public function deleteMandate(string $payerNr)
    {
        $this->treeBuilder->addDeleteMandateRecord($payerNr);
    }
}
