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
     * @When I request mandate :payerNr be deleted
     */
    public function iRequestMandateBeDeleted($payerNr)
    {
        $this->writer->deleteMandate($payerNr);
    }

    /**
     * @When I request mandate :payerNr be added
     */
    public function iRequestMandateBeAdded($payerNr)
    {
        $this->writer->addNewMandate(
            $payerNr,
            (new \byrokrat\banking\AccountFactory)->createAccount('50001111116'),
            new \byrokrat\id\PersonalId('820323-2775')
        );
    }

    /**
     * @When I request mandate :payerNr be accepted
     */
    public function iRequestMandateBeAccepted($payerNr)
    {
        $this->writer->acceptDigitalMandate($payerNr);
    }

    /**
     * @When I request mandate :payerNr be rejected
     */
    public function iRequestMandateBeRejected($payerNr)
    {
        $this->writer->rejectDigitalMandate($payerNr);
    }

    /**
     * @When I request mandate :payerNr be updated to :newPayerNr
     */
    public function iRequestMandateBeUpdatedTo($payerNr, $newPayerNr)
    {
        $this->writer->updateMandate($payerNr, $newPayerNr);
    }

    /**
     * @When I request a payment of :amount SEK from :payerNr
     */
    public function iRequestAPaymentOfSekFrom($amount, $payerNr)
    {
        $this->writer->addPayment($payerNr, new SEK($amount), new \DateTime);
    }

    /**
     * @When I request a payment of :amount SEK to :payerNr
     */
    public function iRequestAPaymentOfSekTo($amount, $payerNr)
    {
        $this->writer->addOutgoingPayment($payerNr, new SEK($amount), new \DateTime);
    }

    /**
     * @When I request a monthly payment of :amount SEK from :payerNr
     */
    public function iRequestAMonthlyPaymentOfSekFrom($amount, $payerNr)
    {
        $this->writer->addMonthlyPayment($payerNr, new SEK($amount), new \DateTime);
    }

    /**
     * @When I request an immediate payment of :amount SEK from :payerNr
     */
    public function iRequestAnImmediatePaymentOfSekFrom($amount, $payerNr)
    {
        $this->writer->addImmediatePayment($payerNr, new SEK($amount));
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
