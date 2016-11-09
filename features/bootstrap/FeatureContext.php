<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use byrokrat\autogiro\ParserFactory;
use byrokrat\autogiro\Enumerator;

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
        $this->fileNode = $this->parser->parse($string->getRaw());
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

    /**
     * @Then I find :number mandate response nodes
     */
    public function iFindMandateResponseNodes($number)
    {
        $count = 0;

        $enumerator = new Enumerator;

        $enumerator->onMandateResponseNode(function () use (&$count) {
            $count++;
        });

        $enumerator->enumerate($this->fileNode);

        $this->assertCount((integer)$number, $count);
    }

    /**
     * @Then I find :number mandate request nodes
     */
    public function iFindMandateRequestNodes($number)
    {
        $count = 0;
        $counter = function () use (&$count) {
            $count++;
        };

        $enumerator = new Enumerator;

        $enumerator->onRequestMandateAcceptanceNode($counter);
        $enumerator->onRequestMandateCreationNode($counter);
        $enumerator->onRequestMandateDeletionNode($counter);
        $enumerator->onRequestMandateRejectionNode($counter);
        $enumerator->onRequestMandateUpdateNode($counter);

        $enumerator->enumerate($this->fileNode);

        $this->assertCount((integer)$number, $count);
    }

    private function assertInArray($needle, array $haystack)
    {
        if (!in_array($needle, $haystack)) {
            throw new Exception("Unable to fins $needle in array");
        }
    }

    private function assertCount($expected, $count)
    {
        if ($expected != $count) {
            throw new Exception("Invalid count $count (expected $expected)");
        }
    }
}
