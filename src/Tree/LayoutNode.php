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

declare(strict_types = 1);

namespace byrokrat\autogiro\Tree;

/**
 * Generic node container
 */
class LayoutNode implements NodeInterface
{
    /**
     * @var OpeningNode
     */
    private $opening;

    /**
     * @var ClosingNode
     */
    private $closing;

    /**
     * @var NodeInterface[]
     */
    private $content;

    public function __construct(OpeningNode $opening, ClosingNode $closing, NodeInterface ...$content)
    {
        $this->opening = $opening;
        $this->closing = $closing;
        $this->content = $content;
    }

    public function getLayoutId(): string
    {
        return $this->getOpeningNode()->getLayoutId();
    }

    public function getOpeningNode(): OpeningNode
    {
        return $this->opening;
    }

    public function getClosingNode(): ClosingNode
    {
        return $this->closing;
    }

    /**
     * @return NodeInterface[]
     */
    public function getContentNodes(): array
    {
        return $this->content;
    }

    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitLayoutNode($this);

        $this->getOpeningNode()->accept($visitor);

        foreach ($this->getContentNodes() as $node) {
            $node->accept($visitor);
        }

        $this->getClosingNode()->accept($visitor);
    }

    public function getLineNr(): int
    {
        return $this->getOpeningNode()->getLineNr();
    }
}
