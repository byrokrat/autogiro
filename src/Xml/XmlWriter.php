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
 * Copyright 2016-18 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Xml;

use byrokrat\autogiro\Tree\Node;

/**
 * Access class to transform node tree into xml
 */
final class XmlWriter
{
    /**
     * @var Stringifier
     */
    private $stringifier;

    public function __construct(Stringifier $stringifier)
    {
        $this->stringifier = $stringifier;
    }

    public function asXml(Node $node): string
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString('  ');
        $writer->startDocument("1.0");
        $node->accept(new XmlWritingVisitor($writer, $this->stringifier));
        $writer->endDocument();

        return $writer->outputMemory();
    }
}
