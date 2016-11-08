# Byrokrat.Autogiro

[![Packagist Version](https://img.shields.io/packagist/v/byrokrat/autogiro.svg?style=flat-square)](https://packagist.org/packages/byrokrat/autogiro)
[![license](https://img.shields.io/github/license/byrokrat/autogiro.svg?maxAge=2592000&style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/byrokrat/autogiro/master.svg?style=flat-square)](https://travis-ci.org/byrokrat/autogiro)
[![Quality Score](https://img.shields.io/scrutinizer/g/byrokrat/autogiro.svg?style=flat-square)](https://scrutinizer-ci.com/g/byrokrat/autogiro)
[![Dependency Status](https://img.shields.io/gemnasium/byrokrat/autogiro.svg?style=flat-square)](https://gemnasium.com/byrokrat/autogiro)

Read and write files for the swedish direct debit system autogirot.

> Please note that this package is under development and the api is subject to change.

Installation
------------
```shell
composer require byrokrat/autogiro
```

Autogiro specifications
-----------------------
This library is developed against the [technichal manual](/rel/autogiro_tekniskmanual_sv.pdf)
(in swedish) of the direct debit system (autogirot) revised 2014-08-05. For
updated versions of this document contact [Bankgirocentralen](http://bgc.se).

Instantiating a parser
----------------------
Create a parser using [ParserFactory](/src/ParserFactory.php).

```php
$factory = new ParserFactory;
$parser = $factory->createParser();
```

The created parser can parse and validate monetary amounts, account numbers and
identification numbers using the [Amount](https://github.com/byrokrat/amount),
[Id](https://github.com/byrokrat/id) and [Banking](https://github.com/byrokrat/banking)
packages respectively.

You can opt out of this functionality using one of the processor constants:

```php
$parser = $factory->createParser(ParserFactory::NO_EXTERNAL_PROCESSORS);
```

When in use access the created objects as follows:

```php
/** @var \byrokrat\amount\Amount */
$amount = $amountNode->getAttribute('amount');

/** @var \byrokrat\id\Id */
$id = $idNode->getAttribute('id');

/** @var \byrokrat\banking\AccountNumber */
$account = $accountNode->getAttribute('account');
```

Parsing
-------

```php
/** @var \byrokrat\autogiro\Tree\FileNode */
$fileNode = $parser->parse($raw_content);
```

Grep nodes based on type using the [Enumerator](/src/Enumerator.php):

```php
$enum = new Enumerator;

$enum->onMandateResponseNode($custom_callback);

$enum->enumerate($fileNode);
```

For a list of possible node types see the [Tree](/src/Tree) namespace;
