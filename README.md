# Autogiro

[![Packagist Version](https://img.shields.io/packagist/v/byrokrat/autogiro.svg?style=flat-square)](https://packagist.org/packages/byrokrat/autogiro)
[![Build Status](https://img.shields.io/travis/byrokrat/autogiro/master.svg?style=flat-square)](https://travis-ci.org/byrokrat/autogiro)
[![Quality Score](https://img.shields.io/scrutinizer/g/byrokrat/autogiro.svg?style=flat-square)](https://scrutinizer-ci.com/g/byrokrat/autogiro)

Read and write files for the swedish direct debit system autogirot.

> For a command line utility that can convert autogiro files to XML see
> [`autogiro2xml`](https://github.com/byrokrat/autogiro2xml).

## Installation

```shell
composer require byrokrat/autogiro
```

## Table of contents

1. [Autogiro specifications](#autogiro-specifications)
1. [Generating autogiro request files](#generating-autogiro-request-files)
1. [Parsing autogiro files](#parsing-autogiro-files)
1. [Accessing nodes using visitors](#accessing-nodes-using-visitors)
1. [Generating XML from node trees](#generating-xml-from-node-trees)
1. [Hacking](#hacking)

## Autogiro specifications

This library is developed against the technichal manual (in swedish) of the
direct debit system (autogirot) revised 2016-12-13. For later versions of this
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

The created parser will by default parse and validate
[monetary amounts](https://github.com/moneyphp/money),
[account numbers](https://github.com/byrokrat/banking) and
[identification numbers](https://github.com/byrokrat/id). Opt out of this
functionality by using one of the visitor constants:

<!-- @include ParserFactory -->
```php
$parser = $factory->createParser(\byrokrat\autogiro\Parser\ParserFactory::VISITOR_IGNORE_OBJECTS);
```

Parsing a file creates a node object.

<!--
    @example AutogiroFile
    @include ParserFactory
    @include RawFile
-->
```php
/** @var \byrokrat\autogiro\Tree\Node $node */
$node = $parser->parse($rawFile);
```

### Accessing special objects

`Account`, `Amount`, `StateId` and `Date` nodes are nested structures, where child
node `Object` contains constructed php objects. Access using something like:

<!--
    @example SpecialObjects
    @include AutogiroFile
-->
```php
$money = $node->getChild('Amount')->getValueFrom('Object');
```

### Walking the parse tree

> A simpler way of doing this is by using visitors. See below.

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

## Accessing nodes using visitors

With the use of visitors nodes can be accessed based on name or type.

<!--
    @include AutogiroFile
    @expectOutput "/Delete mandate request found!/"
-->
```php
class MyVisitor extends \byrokrat\autogiro\Visitor\Visitor {
    function beforeDeleteMandateRequest($node) {
        echo "Delete mandate request found!";
    }
}

$visitor = new MyVisitor;

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

### Finding mandate responses

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

### Finding payment responses

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

## Generating XML from node trees

Using this feature can be very helpful to understand how the parser interprets
various layouts.

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

With [composer](https://getcomposer.org/) installed as `composer`

```shell
make
```

Or use something like

```shell
make COMPOSER_CMD=./composer.phar
```
