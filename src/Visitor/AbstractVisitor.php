<?php
/**
 * This file is part of byrokrat\autogiro.
 *
 * byrokrat\autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat\autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat\autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016 Hannes Forsgård
 */

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree;

/**
 * Null implementation to extend by visitors that only imlement some of the actions
 */
abstract class AbstractVisitor implements Tree\VisitorInterface
{
    public function visitLayoutNode(Tree\LayoutNode $node)
    {
    }

    public function visitOpeningNode(Tree\OpeningNode $node)
    {
    }

    public function visitClosingNode(Tree\ClosingNode $node)
    {
    }

    public function visitRequestMandateNode(Tree\RequestMandateNode $node)
    {
    }

    public function visitRequestMandateRemovalNode(Tree\RequestMandateRemovalNode $node)
    {
    }

    public function visitRequestMandateChangeNode(Tree\RequestMandateChangeNode $node)
    {
    }

    public function visitRequestIncomingTransactionNode(Tree\RequestIncomingTransactionNode $node)
    {
    }

    public function visitRequestOutgoingTransactionNode(Tree\RequestOutgoingTransactionNode $node)
    {
    }

    public function visitRequestTransactionRemovalNode(Tree\RequestTransactionRemovalNode $node)
    {
    }

    public function visitRequestTransactionChangeNode(Tree\RequestTransactionChangeNode $node)
    {
    }

    public function visitMandateResponseNode(Tree\MandateResponseNode $node)
    {
    }
}
