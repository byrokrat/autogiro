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

use byrokrat\autogiro\Processor\Processor;

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
     * @var Processor Helper used to validate and process tree
     */
    private $processor;

    /*public function __construct(TreeBuilder $treeBuilder, PrintingVisitor $printer, Processor $processor)
    {
        $this->treeBuilder = $treeBuilder;
        $this->printer = $printer;
        $this->processor = $processor;
    }*/

    public function deleteMandate(string $payerNr)
    {
        $this->treeBuilder->addDeleteMandateRecord($payerNr);
    }

    public function writeTo(OutputInterface $output)
    {
        $tree = $this->treeBuilder->buildTree();

        /*
            TODO
            det här ser bra ut, men:

            1) [KLAR] ErrorObject -> registrerar error
            1.5) TreeException som ersättning till ParserException
            2) [KLAR] (sånär som på rätt undantag...) ContainingVisitor -> hanterar errors och visitors
            3) [KLAR]ErrorAwareVisitor för att slippa uppning
            3.5) [KLAR] Interface Visitors med flag-args (kan föras över till VisitorFactory)
            4) För över alla processors till visitor..
            4.5) VisitorFactory::createVisitors(Visitors::VISITOR_IGNORE_EXTERNAL)
            5) ParserFactory extends VisitorFactory
                return new Parser(new Grammar, $this->createVisitors($flags));
            6) WriterFactory extends ParserFactory
                om vi vill använda parser för att validera att den genererade koden
                verkligen är så bra som vi tror...
            7) Glöm inte bort att skriva spec för den här klassen...
            8) Möjligtvis flytta upp Parser + factory till eget namespace ??
                då följer allting samma mall...
         */

        $tree->accept($this->processor);

        $this->printer->setOutput($output);
        $tree->accept($this->printer);
    }
}
