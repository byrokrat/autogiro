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

namespace byrokrat\autogiro\Tree;

/**
 * Node visitor interface
 */
interface VisitorInterface
{
    public function visitLayoutNode(LayoutNode $node);
    public function visitOpeningNode(OpeningNode $node);
    public function visitClosingNode(ClosingNode $node);
    public function visitRequestMandateNode(RequestMandateNode $node);
    public function visitRequestMandateRemovalNode(RequestMandateRemovalNode $node);
    public function visitRequestMandateChangeNode(RequestMandateChangeNode $node);
    public function visitRequestIncomingTransactionNode(RequestIncomingTransactionNode $node);
    public function visitRequestOutgoingTransactionNode(RequestOutgoingTransactionNode $node);
    public function visitRequestTransactionRemovalNode(RequestTransactionRemovalNode $node);
    public function visitRequestTransactionChangeNode(RequestTransactionChangeNode $node);
    public function visitMandateResponseNode(MandateResponseNode $node);
}
