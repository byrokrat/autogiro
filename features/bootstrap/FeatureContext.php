<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use byrokrat\autogiro\Parser\ParserFactory;
use byrokrat\autogiro\Enumerator;
use byrokrat\autogiro\Exception\TreeException;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @var \byrokrat\autogiro\Parser
     */
    private $parser;

    /**
     * @var \byrokrat\autogiro\Tree\FileNode Created at parse time
     */
    private $fileNode;

    /**
     * @var TreeException
     */
    private $exception;

    /**
     * @Given a parser
     */
    public function aParser()
    {
        $this->parser = (new ParserFactory)->createParser();
    }

    /**
     * @Given a parser that ignores account and id structures
     */
    public function aParserThatIgnoresAccountAndIdStructures()
    {
        $this->parser = (new ParserFactory)->createParser(
            ParserFactory::VISITOR_IGNORE_ACCOUNTS | ParserFactory::VISITOR_IGNORE_IDS
        );
    }

    /**
     * @When I parse:
     */
    public function iParse(PyStringNode $string)
    {
        try {
            $this->fileNode = $this->parser->parse($string->getRaw());
        } catch (TreeException $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then I find a :layoutType layout
     */
    public function iFindALayout($layoutType)
    {
        if ($this->exception) {
            throw $this->exception;
        }

        $layoutIds = [];

        foreach ($this->fileNode->getChildren() as $layoutNode) {
            $layoutIds[] = $layoutNode->getAttribute('layout_name');
        }

        $this->assertInArray(
            constant("byrokrat\autogiro\Layouts::$layoutType"),
            $layoutIds,
            $this->fileNode->getAttribute('layout_ids')
        );
    }

    /**
     * @Then I find :number :nodeType nodes
     */
    public function iFindNodes($number, $nodeType)
    {
        $enumerator = new Enumerator;

        $count = 0;
        $enumerator->on($nodeType, function () use (&$count) {
            $count++;
        });

        $enumerator->enumerate($this->fileNode);

        $this->assertCount((integer)$number, $count);
    }

    /**
     * @Then I get a :error error
     */
    public function iGetAError(string $error)
    {
        $this->assertInArray(
            $error,
            $this->exception->getErrors()
        );
    }

    private function assertInArray($needle, array $haystack)
    {
        if (!in_array($needle, $haystack)) {
            throw new Exception("Unable to find $needle in array");
        }
    }

    private function assertCount($expected, $count)
    {
        if ($expected != $count) {
            throw new Exception("Invalid count $count (expected $expected)");
        }
    }
}
