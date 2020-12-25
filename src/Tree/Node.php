<?php

/**
 * This file is part of byrokrat\autogiro.
 *
 * byrokrat\autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat\autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat\autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016-20 Hannes Forsgård
 */

declare(strict_types=1);

namespace byrokrat\autogiro\Tree;

use byrokrat\autogiro\Visitor\VisitorInterface;

/**
 * Defines a node in the parse tree
 */
class Node
{
    public const ACCOUNT = 'Account';
    public const ACTIVE_YEAR = 'ActiveYear';
    public const ADRESS_1 = 'Adress1';
    public const ADRESS_2 = 'Adress2';
    public const ADRESS_3 = 'Adress3';
    public const ADRESS_4 = 'Adress4';
    public const AMOUNT = 'Amount';
    public const CITY = 'City';
    public const CLOSING = 'Closing';
    public const COMMENT = 'Comment';
    public const CREATED = 'Created';
    public const DATE = 'Date';
    public const DIRECTION = 'Direction';
    public const INFO = 'Info';
    public const INTERVAL = 'Interval';
    public const MAX_AMOUNT = 'MaxAmount';
    public const NEW_DATE = 'NewDate';
    public const NUMBER = 'Number';
    public const OBJ = 'Object';
    public const OPENING = 'Opening';
    public const PAYEE_BANKGIRO = 'PayeeBankgiro';
    public const PAYEE_BGC_NUMBER = 'PayeeBgcNumber';
    public const PAYER_NUMBER = 'PayerNumber';
    public const POST_CODE = 'Postcode';
    public const REFERENCE = 'Reference';
    public const REFUND_DATE = 'RefundDate';
    public const REPETITIONS = 'Repetitions';
    public const SERIAL_NUMBER = 'Serial';
    public const STATE_ID = 'StateId';
    public const STATUS = 'Status';
    public const TYPE = 'Type';
    public const UPDATED = 'Updated';
    public const VALID_FROM_DATE = 'ValidFromDate';

    public const AMENDMENT_FLAG = 'AmendmentFlag';
    public const CREATED_FLAG = 'CreatedFlag';
    public const DELETED_FLAG = 'DeletedFlag';
    public const ERROR_FLAG = 'ErrorFlag';
    public const FAILED_FLAG = 'FailedFlag';
    public const REVOCATION_FLAG = 'RevocationFlag';
    public const SUCCESSFUL_FLAG = 'SuccessfulFlag';

    public const AUTOGIRO_REQUEST_FILE = 'AutogiroRequestFile';

    public const MANDATE_REQUEST_SECTION = 'MandateRequestSection';
    public const ACCEPT_DIGITAL_MANDATE_REQUEST = 'AcceptDigitalMandateRequest';
    public const CREATE_MANDATE_REQUEST = 'CreateMandateRequest';
    public const DELETE_MANDATE_REQUEST = 'DeleteMandateRequest';
    public const REJECT_DIGITAL_MANDATE_REQUEST = 'RejectDigitalMandateRequest';
    public const UPDATE_MANDATE_REQUEST = 'UpdateMandateRequest';
    public const NEW_PAYER_NUMBER = 'NewPayerNumber';
    public const OLD_PAYER_NUMBER = 'OldPayerNumber';
    public const NEW_PAYEE_BANKGIRO = 'NewPayeeBankgiro';
    public const OLD_PAYEE_BANKGIRO = 'OldPayeeBankgiro';

    public const PAYMENT_REQUEST_SECTION = 'PaymentRequestSection';
    public const INCOMING_PAYMENT_REQUEST = 'IncomingPaymentRequest';
    public const OUTGOING_PAYMENT_REQUEST = 'OutgoingPaymentRequest';

    public const AMENDMENT_REQUEST_SECTION = 'AmendmentRequestSection';
    public const AMENDMENT_REQUEST = 'AmendmentRequest';

    public const AUTOGIRO_PAYMENT_RESPONSE_FILE = 'AutogiroPaymentResponseFile';

    public const INCOMING_PAYMENT_RESPONSE_SECTION = 'IncomingPaymentResponseSection';
    public const INCOMING_PAYMENT_RESPONSE_SECTION_OPENING = 'IncomingPaymentResponseSectionOpening';
    public const INCOMING_PAYMENT_COUNT = 'IncomingPaymentCount';
    public const FAILED_INCOMING_PAYMENT_RESPONSE = 'FailedIncomingPaymentResponse';
    public const SUCCESSFUL_INCOMING_PAYMENT_RESPONSE = 'SuccessfulIncomingPaymentResponse';

    public const OUTGOING_PAYMENT_RESPONSE_SECTION = 'OutgoingPaymentResponseSection';
    public const OUTGOING_PAYMENT_RESPONSE_SECTION_OPENING = 'OutgoingPaymentResponseSectionOpening';
    public const OUTGOING_PAYMENT_COUNT = 'OutgoingPaymentCount';
    public const SUCCESSFUL_OUTGOING_PAYMENT_RESPONSE = 'SuccessfulOutgoingPaymentResponse';
    public const FAILED_OUTGOING_PAYMENT_RESPONSE = 'FailedOutgoingPaymentResponse';

    public const REFUND_PAYMENT_RESPONSE_SECTION = 'RefundPaymentResponseSection';
    public const REFUND_PAYMENT_RESPONSE_SECTION_OPENING = 'RefundPaymentResponseSectionOpening';
    public const REFUND_PAYMENT_COUNT = 'RefundPaymentCount';
    public const REFUND_PAYMENT_RESPONSE = 'RefundPaymentResponse';

    public const AUTOGIRO_PAYMENT_RESPONSE_OLD_FILE = 'AutogiroPaymentResponseOldFile';
    public const OUTGOING_PAYMENT_RESPONSE = 'OutgoingPaymentResponse';
    public const INCOMING_PAYMENT_RESPONSE = 'IncomingPaymentResponse';

    public const AUTOGIRO_MANDATE_RESPONSE_FILE = 'AutogiroMandateResponseFile';
    public const MANDATE_RESPONSE = 'MandateResponse';

    public const DIGITAL_MANDATE_FILE = 'DigitalMandateFile';
    public const DIGITAL_MANDATE_COUNT = 'DigitalMandateCount';
    public const DIGITAL_MANDATE = 'DigitalMandate';

    public const AUTOGIRO_PAYMENT_REJECTION_FILE = 'AutogiroPaymentRejectionFile';
    public const INCOMING_PAYMENT_REJECTION_RESPONSE = 'IncomingPaymentRejectionResponse';
    public const OUTGOING_PAYMENT_REJECTION_RESPONSE = 'OutgoingPaymentRejectionResponse';

    public const AUTOGIRO_AMENDMENT_RESPONSE_FILE = 'AutogiroAmendmentResponseFile';
    public const SUCCESSFUL_INCOMING_AMENDMENT_RESPONSE = 'SuccessfulIncomingAmendmentResponse';
    public const SUCCESSFUL_OUTGOING_AMENDMENT_RESPONSE = 'SuccessfulOutgoingAmendmentResponse';
    public const SUCCESSFUL_AMENDMENT_RESPONSE = 'SuccessfulAmendmentResponse';
    public const FAILED_INCOMING_AMENDMENT_RESPONSE = 'FailedIncomingAmendmentResponse';
    public const FAILED_OUTGOING_AMENDMENT_RESPONSE = 'FailedOutgoingAmendmentResponse';
    public const FAILED_AMENDMENT_RESPONSE = 'FailedAmendmentResponse';

    public const AUTOGIRO_PAYMENT_EXTRACT_FILE = 'AutogiroPaymentExtractFile';
    public const INCOMING_PAYMENT_EXTRACT = 'IncomingPayment';
    public const OUTGOING_PAYMENT_EXTRACT = 'OutgoingPayment';

    public const AUTOGIRO_MANDATE_EXTRACT_FILE = 'AutogiroMandateExtractFile';
    public const MANDATE_EXTRACT = 'Mandate';

    /**
     * @var Node[]
     */
    private $children = [];

    /**
     * @var int
     */
    private $lineNr = 0;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct(int $lineNr = 0, $value = null)
    {
        $this->lineNr = $lineNr;
        $this->value = $value;
        $this->name = basename(str_replace('\\', '/', get_class($this)));
    }

    /**
     * Accept a visitor
     */
    public function accept(VisitorInterface $visitor): void
    {
        $visitor->visitBefore($this);

        foreach ($this->getChildren() as $node) {
            $node->accept($visitor);
        }

        $visitor->visitAfter($this);
    }

    /**
     * Get line number this node definition started at
     */
    public function getLineNr(): int
    {
        return $this->lineNr;
    }

    /**
     * Get node name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set a custom node name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get node type identifier
     */
    public function getType(): string
    {
        return basename(str_replace('\\', '/', get_class()));
    }

    /**
     * Get raw value wrapped by node
     *
     * @return mixed Loaded node value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Check if this is a null object implementation
     */
    public function isNull(): bool
    {
        return false;
    }

    /**
     * Set a child node
     */
    public function addChild(Node $node): void
    {
        $this->children[] = $node;
    }

    /**
     * Get child node
     */
    public function getChild(string $name): Node
    {
        foreach ($this->children as $node) {
            if (strcasecmp($node->getName(), $name) == 0) {
                return $node;
            }
        }

        return new NullNode();
    }

    /**
     * Check if child exists
     */
    public function hasChild(string $name): bool
    {
        foreach ($this->children as $node) {
            if (strcasecmp($node->getName(), $name) == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get registered child nodes
     *
     * @return Node[]
     */
    public function getChildren(string $name = ''): array
    {
        $nodes = [];

        foreach ($this->children as $node) {
            if (!$name || strcasecmp($node->getName(), $name) == 0) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    /**
     * Get raw value wrapped in child node
     *
     * @return mixed Loaded node value
     */
    public function getValueFrom(string $name)
    {
        return $this->getChild($name)->getValue();
    }

    /**
     * Get raw value wrapped in child 'object' node
     *
     * @return mixed Loaded node value
     */
    public function getObjectValue()
    {
        return $this->getChild(self::OBJ)->getValue();
    }
}
