# Changes in 1.0-alpha2

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
