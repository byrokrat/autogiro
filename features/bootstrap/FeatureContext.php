<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Parser\ParserFactory;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Writer\WriterFactory;
use byrokrat\autogiro\Exception\ContentException;
use byrokrat\amount\Currency\SEK;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    use ParserTrait;

    /**
     * @var \byrokrat\autogiro\Tree\AutogiroFile Created at parse time
     */
    private $fileNode;

    /**
     * @var ContentException
     */
    private $exception;

    /**
     * @var \byrokrat\autogiro\Writer\Writer
     */
    private $writer;

    /**
     * @var string The generated request file
     */
    private $generatedFile = '';

    /**
     * @When I parse:
     */
    public function iParse(PyStringNode $node)
    {
        $this->parseRawFile(Utils::normalize($node));
    }

    /**
     * @Then I find a :expectedFileType layout
     */
    public function iFindALayout($expectedFileType)
    {
        if ($this->exception) {
            throw $this->exception;
        }

        Assertions::assertEquals(
            $expectedFileType,
            $this->fileNode->getName()
        );
    }

    /**
     * @Then I find :number :nodeType nodes
     */
    public function iFindNodes($number, $nodeType)
    {
        $visitor = new Visitor;
        $count = 0;

        $visitor->before($nodeType, function () use (&$count) {
            $count++;
        });

        $this->fileNode->accept($visitor);

        Assertions::assertEquals((integer)$number, $count);
    }

    /**
     * @Then the last :nodeType contains account :number
     */
    public function theLastNodeContainsAccount(string $nodeType, string $number)
    {
        $nodes = $this->fileNode->getChildren($nodeType);

        Assertions::assertEquals(
            $number,
            array_pop($nodes)->getChild('Account')->getValueFrom('Object')->getNumber()
        );
    }

    /**
     * @Then I get a :error error
     */
    public function iGetErrorWithMessage(string $error)
    {
        Assertions::assertInArray(
            $error,
            $this->exception->getErrors()
        );
    }

    /**
     * @Then I get an error
     */
    public function iGetAnError()
    {
        Assertions::assertEquals(
            ContentException::CLASS,
            get_class($this->exception)
        );
    }

    /**
     * @Given a writer with BGC number :bgcNumber, bankgiro :autogiro and date :date
     */
    public function aWriterWithBgcNumberAndBankgiro(string $bgcNumber, string $bankgiro, string $date)
    {
        $this->writer = (new WriterFactory)->createWriter(
            $bgcNumber,
            (new \byrokrat\banking\BankgiroFactory)->createAccount($bankgiro),
            new \DateTime($date)
        );
    }

    /**
     * @Transform table:type,value
     */
    public function castArgumentsTable(TableNode $argsTable)
    {
        $args = [];

        foreach ($argsTable->getHash() as $argHash) {
            switch ($argHash['type']) {
                case 'string':
                    $args[] = $argHash['value'];
                    break;
                case 'account':
                    $args[] = (new \byrokrat\banking\AccountFactory)->createAccount($argHash['value']);
                    break;
                case 'id':
                    $args[] = new \byrokrat\id\PersonalId($argHash['value']);
                    break;
                case 'SEK':
                    $args[] = new SEK($argHash['value']);
                    break;
                case 'Date':
                    $args[] = new \DateTime($argHash['value']);
                    break;
                default:
                    throw new \Exception("Unknown argument type: {$argHash['type']}");
            }
        }

        return $args;
    }

    /**
     * @When I call writer method :method with arguments:
     */
    public function iCallWriterMethodWithArguments($method, array $args)
    {
        $this->writer->$method(...$args);
    }

    /**
     * @When I call writer method :method with argument :arg
     */
    public function iCallWriterMethodWithArgument($method, $arg)
    {
        $this->writer->$method($arg);
    }

    /**
     * @When I generate the request file
     */
    public function iGenerateTheRequestFile()
    {
        $this->generatedFile = $this->writer->getContent();
    }

    /**
     * @When I parse the generated file
     */
    public function iParseTheGeneratedFile()
    {
        $this->parseRawFile($this->generatedFile);
    }

    /**
     * @Then I get a file like:
     */
    public function iGetAFileLike(PyStringNode $node)
    {
        if (Utils::normalize($node) != $this->generatedFile) {
            throw new \Exception("Unvalid generated file: $this->generatedFile");
        }
    }

    private function parseRawFile(string $content)
    {
        try {
            $this->fileNode = $this->getParser()->parse($content);
        } catch (ContentException $e) {
            $this->exception = $e;
        }
    }
}
