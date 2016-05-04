<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

/**
 * Layout identifiers
 */
interface Layouts
{
    /**
     * Layout for sending mandate requests
     */
    const LAYOUT_MANDATE_REQUEST = 'LAYOUT_MANDATE_REQUEST';

    /**
     * Layout for receiving responses the previously made mandate requests
     */
    const LAYOUT_MANDATE_RESPONSE = 'LAYOUT_MANDATE_RESPONSE';

    /**
     * Layout for receiving new digital mandates
     */
    const LAYOUT_DIGITAL_MANDATE = 'LAYOUT_DIGITAL_MANDATE';

    /**
     * Layout for receiving the current list of mandates
     */
    const LAYOUT_MANDATE_REPORT = 'LAYOUT_MANDATE_REPORT';

    /**
     * Layout for sending payment requests
     */
    const LAYOUT_PAYMENT_REQUEST = 'LAYOUT_PAYMENT_REQUEST';

    /**
     * Layout for receiving responses to previously made payment requests
     */
    const LAYOUT_PAYMENT_RESPONSE = 'LAYOUT_PAYMENT_RESPONSE';

    /**
     * Layout for receiving responses to previously made payment requests in BgMax format
     */
    const LAYOUT_PAYMENT_RESPONSE_BGMAX = 'LAYOUT_PAYMENT_RESPONSE_BGMAX';

    /**
     * Layout for sending payment amendments and revocations
     */
    const LAYOUT_AMENDMENT_REQUEST = 'LAYOUT_AMENDMENT_REQUEST';

    /**
     * Layout for receiving responses to previously made payment amendments and revocations
     */
    const LAYOUT_AMENDMENT_RESPONSE = 'LAYOUT_AMENDMENT_RESPONSE';

    /**
     * Layout for receiving rejected payments
     */
    const LAYOUT_PAYMENT_REJECTION = 'LAYOUT_PAYMENT_REJECTION';

    /**
     * Layout for receiving the current list of comming payments
     */
    const LAYOUT_PAYMENT_REPORT = 'LAYOUT_PAYMENT_REPORT';
}
