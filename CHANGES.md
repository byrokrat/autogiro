# Notable changes in 1.0-alpha3

## Misc

* Added `autogiro2xml` executable.
* Removed the `Messages` and `Layouts` interfaces. Inspect node names and flags instead.
* `VISITOR_IGNORE_OBJECTS` instead of `VISITOR_IGNORE_EXTERNAL`.
* Typehint on `WriterInterface` instead of `Writer`.

## Node

* Added `Node::getName()` behaving as did previously `Node::getType()`.
* Changed `Node::getType()` to now return classname where defined (eg. in the
  `Type` class of node).
* Renamed `Node::setChild()` => `Node::addChild()`.
* `Node::getValue()` now returns `mixed` instead of `string`.
* `Node::getChild()` now returns a `NullNode` if child is missing.
* Added `Node::isNull()` to check for null object implementation.
* Added `Node::setName()` to enable dynamic node naming.
* Removed support for node attributes.

## Visitor

* Dispatches on both node name and type.

## Node tree

* `Account`, `Amount`, `StateId`, `Date` and `Message` nodes are now nested structures.
* Removed the `Node` suffix from all node classes (except `NullNode`).
* Using generic `Closing` and `Opening` records.
* Renamed `BankgiroNode` => `PayeeBankgiro`.
* Renamed `BgcNumberNode` => `PayeeBgcNumber`.
* Renamed `FileNode` => `AutogiroFile`.
* Renamed `IdNode` => `StateId`.
* Renamed `IncomingPaymentResponseOpening` => `IncomingPaymentResponseSectionOpening`
* Renamed `OutgoingPaymentResponseOpening` => `OutgoingPaymentResponseSectionOpening`
* Renamed `RefundPaymentResponseOpening` => `RefundPaymentResponseSectionOpening`
