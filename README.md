# Autogiro

[![Packagist Version](https://img.shields.io/packagist/v/byrokrat/autogiro.svg?style=flat-square)](https://packagist.org/packages/byrokrat/autogiro)
[![Build Status](https://img.shields.io/travis/byrokrat/autogiro/master.svg?style=flat-square)](https://travis-ci.org/byrokrat/autogiro)
[![Quality Score](https://img.shields.io/scrutinizer/g/byrokrat/autogiro.svg?style=flat-square)](https://scrutinizer-ci.com/g/byrokrat/autogiro)

Read and write files for the swedish direct debit system autogirot.

## Installation

```shell
composer require byrokrat/autogiro:^1.0@alpha
```

## Autogiro specifications

This library is developed against the technichal manual (in swedish) of the
direct debit system (autogirot) revised 2016-12-13. For current versions of this
document see [Bankgirocentralen](http://bgc.se).

## Generating autogiro request files

Create a writer by supplying your *bankgiro account number* and
*BGC customer number* to [`WriterFactory`](/src/Writer/WriterFactory.php).

<!-- @example WriterFactory -->
```php
$writer = (new \byrokrat\autogiro\Writer\WriterFactory)->createWriter(
    '123456',
    (new \byrokrat\banking\BankgiroFactory)->createAccount('1111-1119')
);
```

Perform actions on the writer and generate file.

<!--
    @include WriterFactory
    @expectOutput /AUTOGIRO/
-->
```php
$writer->deleteMandate('1234567890');
$rawFile = $writer->getContent();
echo $rawFile;
```

Will output something like:

```
0120180114AUTOGIRO                                            1234560011111119  
0300111111190000001234567890                                                    
```

<!--
    @example RawFile
    @include WriterFactory
```php
$writer->deleteMandate('1234567890');
$rawFile = $writer->getContent();
```
-->

## Parsing autogiro files

Create a parser using the [`ParserFactory`](/src/Parser/ParserFactory.php).

<!-- @example ParserFactory -->
```php
$factory = new \byrokrat\autogiro\Parser\ParserFactory;
$parser = $factory->createParser();
```

The created parser can parse and validate monetary amounts, account numbers and
identification numbers using the [Amount](https://github.com/byrokrat/amount),
[Id](https://github.com/byrokrat/id) and [Banking](https://github.com/byrokrat/banking)
packages respectively. Opt out of this functionality by using one of the visitor constants:

<!-- @include ParserFactory -->
```php
$parser = $factory->createParser(\byrokrat\autogiro\Parser\ParserFactory::VISITOR_IGNORE_EXTERNAL);
```

Parsing an autogiro file creates a `AutogiroFile`.

<!--
    @example AutogiroFile
    @include ParserFactory
    @include RawFile
-->
```php
/** @var \byrokrat\autogiro\Tree\AutogiroFile $node */
$node = $parser->parse($rawFile);
```

### Walking the parse tree

Walk the tree by calling `hasChild()`, `getChild()` and `getChildren()`.

<!--
    @example GetChild
    @include AutogiroFile
    @expectOutput "0000001234567890"
-->
```php
echo $node->getChild('MandateRequestSection')
    ->getChild('DeleteMandateRequest')
    ->getChild('PayerNumber')
    ->getValue();
```

Or access all `DeleteMandateRequest` nodes.

> NOTE! A simpler way of doing this is by using visitors. See below.

<!--
    @example GetChildren
    @include AutogiroFile
-->
```php
foreach ($node->getChild('MandateRequestSection')->getChildren('DeleteMandateRequest') as $child) {
    // process...
}
```

Trying to access a child that does not exist returns a `NullNode`.

<!--
    @example NullNode
    @include AutogiroFile
    @expectOutput "1"
-->
```php
echo $node->getChild('this-does-not-exist')
    ->getChild('and-neither-does-this')
    ->isNull();
```

> NOTE! This package contains a simple command line tool (`autogiro2xml`) for
> converting autogiro files to a more readable XML format suitable for visualy
> examining parse trees.

### Accessing special objects

`Account`, `Amount`, `StateId` and `Date` nodes are nested structures.
Child node `Number` (or `Text`) contains the raw parsed content and child node
`Object` contains php objects.

With an `Account` node you could for example do the following:

<!--
    @example SpecialObjects
    @include AutogiroFile
-->
```php
/** @var string $rawNumber */
$rawNumber = $node->getChild('Number')->getValue();

/** @var \byrokrat\banking\AccountNumber $account */
$account = $node->getChild('Object')->getValue();
```

## Grep nodes based on name

<!--
    @include AutogiroFile
    @expectOutput "/Delete mandate request found!/"
-->
```php
$visitor = new class extends \byrokrat\autogiro\Visitor\Visitor {
    function beforeDeleteMandateRequest($node) {
        echo "Delete mandate request found!";
    }
};

$node->accept($visitor);
```

This can also be done dynamically.

<!--
    @example Visitor
    @include AutogiroFile
-->
```php
$visitor = new \byrokrat\autogiro\Visitor\Visitor;

$visitor->before("DeleteMandateRequest", function ($node) {
    echo "Delete mandate request found!";
});
```

### Find mandate responses

<!--
    @example Mandate-response-recipe
    @include Visitor
-->
```php
$visitor->before("MandateResponse", function ($node) {
    if ($node->hasChild('CreatedFlag')) {
        // Mandate successfully created
    }
    if ($node->hasChild('DeletedFlag')) {
        // Mandate successfully deleted
    }
    if ($node->hasChild('ErrorFlag')) {
        // Mandate error state
    }
});
```

### Find payment responses

<!--
    @example Payment-response-recipe
    @include Visitor
-->
```php
$visitor->before("SuccessfulIncomingPaymentResponse", function ($node) {
    // successfull payment..
});

$visitor->before("FailedIncomingPaymentResponse", function ($node) {
    // failed payment..
});
```

## Generate XML from node trees

Autogiro is able to generate XML from node trees. Using this feature can be very
helpful to understand how the parser interprets the various layouts.

<!--
    @include ParserFactory
    @include RawFile
    @expectOutput "/^<\?xml version=/"
-->
```php
$xmlWriter = (new \byrokrat\autogiro\Xml\XmlWriterFactory)->createXmlWriter();

echo $xmlWriter->asXml(
    $parser->parse($rawFile)
);
```

## Hacking

Tweak as needed.

```shell
composer install
composer global require chh/bob:^1.0@alpha
export PATH=$PATH:$HOME/.composer/vendor/bin/
bob install_dev_tools
bob
```
