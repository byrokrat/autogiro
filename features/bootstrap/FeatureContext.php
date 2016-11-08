<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use byrokrat\autogiro\ParserFactory;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $parser;

    private $fileNode;

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
            ParserFactory::NO_ACCOUNT_PROCESSOR | ParserFactory::NO_ID_PROCESSOR
        );
    }

    /**
     * @When I parse:
     */
    public function iParse(PyStringNode $string)
    {
        // TODO extra nyrad på slutet ska inte behövas!!
        $this->fileNode = $this->parser->parse($string->getRaw() . "\n");
    }

    /**
     * @Then I find a :layoutType layout
     */
    public function iFindALayout($layoutType)
    {
        $this->assertInArray(
            constant("byrokrat\autogiro\Layouts::$layoutType"),
            $this->fileNode->getAttribute('layout_ids')
        );
    }

    private function assertInArray($needle, array $haystack)
    {
        if (!in_array($needle, $haystack)) {
            throw new Exception("Unable to fins $needle in array");
        }
    }
}
