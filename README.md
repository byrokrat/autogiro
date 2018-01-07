# Autogiro

[![Packagist Version](https://img.shields.io/packagist/v/byrokrat/autogiro.svg?style=flat-square)](https://packagist.org/packages/byrokrat/autogiro)
[![license](https://img.shields.io/github/license/byrokrat/autogiro.svg?maxAge=2592000&style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/byrokrat/autogiro/master.svg?style=flat-square)](https://travis-ci.org/byrokrat/autogiro)
[![Quality Score](https://img.shields.io/scrutinizer/g/byrokrat/autogiro.svg?style=flat-square)](https://scrutinizer-ci.com/g/byrokrat/autogiro)
[![Dependency Status](https://img.shields.io/gemnasium/byrokrat/autogiro.svg?style=flat-square)](https://gemnasium.com/byrokrat/autogiro)

Read and write files for the swedish direct debit system autogirot.

Installation
------------
```shell
composer require byrokrat/autogiro:^1.0@alpha
```

Autogiro specifications
-----------------------
This library is developed against the technichal manual (in swedish) of the
direct debit system (autogirot) revised 2016-12-13. For current versions of this
document see [Bankgirocentralen](http://bgc.se).

Instantiating a parser
----------------------
Create a parser using [ParserFactory](/src/Parser/ParserFactory.php).

<!--
    @example factory-n-parser
-->
```php
$factory = new \byrokrat\autogiro\Parser\ParserFactory;
$parser = $factory->createParser();
```

The created parser can parse and validate monetary amounts, account numbers and
identification numbers using the [Amount](https://github.com/byrokrat/amount),
[Id](https://github.com/byrokrat/id) and [Banking](https://github.com/byrokrat/banking)
packages respectively.

You can opt out of this functionality using one of the visitor constants:

<!--
    @extends factory-n-parser
-->
```php
$parser = $factory->createParser(\byrokrat\autogiro\Parser\ParserFactory::VISITOR_IGNORE_EXTERNAL);
```

When in use access the created objects as follows:

<!-- @ignore -->
```php
/** @var \byrokrat\amount\Amount $amount */
$amount = $amountNode->getAttribute('amount');

/** @var \byrokrat\id\IdInterface $id */
$id = $idNode->getAttribute('id');

/** @var \byrokrat\banking\AccountNumber $account */
$account = $accountNode->getAttribute('account');
```

Parsing
-------

<!-- @ignore -->
```php
/** @var \byrokrat\autogiro\Tree\FileNode $fileNode */
$fileNode = $parser->parse($raw_content);
```

Grep nodes based on type using the [Enumerator](/src/Enumerator.php):

<!-- @ignore -->
```php
$enum = new Enumerator;

$enum->onMandateResponseRecord($custom_callback);

$enum->enumerate($fileNode);
```

For a list of possible node types see the [Tree](/src/Tree) namespace;

Generating autogiro request files
---------------------------------
Create a writer by supplying your bankgiro and BGC customer numbers to `WriterFactory`.

<!-- @example WriterFactory -->
```php
$writer = (new \byrokrat\autogiro\Writer\WriterFactory)->createWriter(
    '123456',
    (new \byrokrat\banking\BankgiroFactory)->createAccount('1111-1119')
);
```

Then perform actions on the writer and generate contents.

<!--
    @extends WriterFactory
    @expectOutput /AUTOGIRO/
-->
```php
$writer->deleteMandate('1234567890');
echo $writer->getContent();
```
