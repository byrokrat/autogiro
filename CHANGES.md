# Notable changes in 1.0-alpha3

## Node

* Added `Node::getName()` behaving as did previously `Node::getType()`.
* Changed `Node::getType()` to now return classname where defined (eg. in the
  `Type` class of node).
* Renamed `Node::setChild()` => `Node::addChild()`.
* `Node::getValue()` now returns `mixed` instead of `string`.
* `Node::getChild()` now returns a `NullNode` if child is missing.
* Added `Node::isNull()` to check for null object implementation.
* Added `Node::setName()` to enable dynamic node naming.

## Visitor

* Now dispatches on both name and type.

## Node tree

* Removed the `Node` suffix from all node classes (except `NullNode`).
* Renamed `BankgiroNode` => `PayeeBankgiro`.
* Renamed `BgcNumberNode` => `PayeeBgcNumber`.
* Renamed `FileNode` => `AutogiroFile`.
* Renamed `IdNode` => `StateId`.

# Notable changes in 1.0-alpha2

## Node tree

Standard nodes renamed:

* `PayeeBgcNumberNode` => `BgcNumberNode`
* `PayeeBankgiroNode` => `BankgiroNode`

Response nodes renamed:

* `Record/OpeningRecordNode` => `Response/ResponseOpening`
* `Record/ClosingRecordNode` => `Response/MandateResponseClosing`
* `Record/Response/MandateResponseNode` => `Response/MandateResponse`

Request nodes renamed:

* `Record/Request/AcceptDigitalMandateRequestNode` => `Request/AcceptDigitalMandateRequest`
* `Record/Request/CreateMandateRequestNode` => `Request/CreateMandateRequest`
* `Record/Request/DeleteMandateRequestNode` => `Request/DeleteMandateRequest`
* `Record/Request/IncomingTransactionRequestNode` => `Request/IncomingPaymentRequest`
* `Record/Request/OutgoingTransactionRequestNode` => `Request/OutgoingPaymentRequest`
* `Record/Request/RejectDigitalMandateRequestNode` => `Request/RejectDigitalMandateRequest`
* `Record/Request/RequestOpeningRecordNode` => `Request/RequestOpening`
* `Record/Request/UpdateMandateRequestNode` => `Request/UpdateMandateRequest`

## Writer

Updated API to match name changes in node tree.
