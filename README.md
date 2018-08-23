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
*BGC customer number* to [`WriterFactory`](/src/Parser/WriterFactory.php).

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

Parsing an autogiro file creates a `FileNode`.

<!--
    @example FileNode
    @include ParserFactory
    @include RawFile
-->
```php
/** @var \byrokrat\autogiro\Tree\FileNode $fileNode */
$fileNode = $parser->parse($rawFile);
```

### Walking the parse tree

Walk the tree by calling `hasChild()`, `getChild()` and `getChildren()`.

<!--
    @example GetChild
    @include FileNode
-->
```php
// @expectOutput "0000001234567890"
echo $fileNode->getChild('MandateRequestSection')
    ->getChild('DeleteMandateRequest')
    ->getChild('PayerNumber')
    ->getChild('Number')
    ->getValue();
```

Or access all `DeleteMandateRequest` nodes.

> NOTE! A simpler way of doing this is by using visitors. See below.

<!--
    @example GetChildren
    @include FileNode
-->
```php
foreach ($fileNode->getChild('MandateRequestSection')->getChildren('DeleteMandateRequest') as $node) {
    // process...
}
```

Trying to access a child that does not exist returns a `NullNode`.

<!--
    @example NullNode
    @include FileNode
    @expectOutput "1"
-->
```php
echo $fileNode->getChild('this-does-not-exist')
    ->getChild('and-neither-does-this')
    ->isNull();
```

> NOTE! This package contains a simple command line tool (`autogiro2xml`) for
> converting autogiro files to a more readable XML format suitable for visualy
> examining parse trees.

Access created objects through attributes.

<!-- @ignore -->
```php
/** @var \byrokrat\amount\Amount $amount */
$amount = $amountNode->getAttribute('amount');

/** @var \byrokrat\id\IdInterface $id */
$id = $idNode->getAttribute('id');

/** @var \byrokrat\banking\AccountNumber $account */
$account = $accountNode->getAttribute('account');
```

## Grep nodes based on type

<!--
    @include FileNode
    @expectOutput "/Delete mandate request found!/"
-->
```php
$visitor = new class extends \byrokrat\autogiro\Visitor\Visitor {
    function beforeDeleteMandateRequest($node) {
        echo "Delete mandate request found!";
    }
};

$fileNode->accept($visitor);
```

This can also be done dynamically.

<!--
    @include FileNode
    @expectOutput "/Delete mandate request found!/"
-->
```php
$visitor = new class extends \byrokrat\autogiro\Visitor\Visitor {};

$dynamicNodeType = "DeleteMandateRequest";

$visitor->{"before$dynamicNodeType"} = function ($node) {
    echo "Delete mandate request found!";
};

$fileNode->accept($visitor);
```

For a list of possible node types see the [Tree](/src/Tree) namespace.

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

Install dependencies

```shell
composer install
```

Install the bob build environment

```shell
composer global require chh/bob:^1.0@alpha
```

If needed put the "global" composer bin dir in your path.

```shell
export PATH=$PATH:$HOME/.composer/vendor/bin/
```

Install development tools

```shell
bob install_dev_tools
```

Build and run tests

```shell
bob
```
