<?php
/**
 * This file is part of byrokrat/autogiro.
 *
 * byrokrat/autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat/autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat/autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016-18 Hannes Forsgård
 */

namespace byrokrat\autogiro;

/**
 * Layout identifiers
 */
interface Layouts
{
    /**
     * Layout for sending requests
     */
    const LAYOUT_REQUEST = 'AutogiroRequestFile';

    /**
     * Layout for receiving responses the previously made mandate requests
     */
    const LAYOUT_MANDATE_RESPONSE = 'AutogiroMandateResponseFile';

    /**
     * Layout for receiving new digital mandates
     */
    const LAYOUT_DIGITAL_MANDATE = 'AutogiroDigitalMandateFile';

    /**
     * Layout for receiving the current list of mandates
     */
    const LAYOUT_MANDATE_REPORT = 'AutogiroMandateReportFile';

    /**
     * Layout for receiving responses to previously made payment requests
     */
    const LAYOUT_PAYMENT_RESPONSE = 'AutogiroPaymentResponseFile';

    /**
     * Layout for receiving responses to previously made payment requests in the old format
     */
    const LAYOUT_PAYMENT_RESPONSE_OLD = 'AutogiroPaymentResponseOldFile';

    /**
     * Layout for receiving responses to previously made payment requests in BgMax format
     */
    const LAYOUT_PAYMENT_RESPONSE_BGMAX = 'AutogiroPaymentResponseBgmaxFile';

    /**
     * Layout for receiving responses to previously made payment amendments and revocations
     */
    const LAYOUT_AMENDMENT_RESPONSE = 'AutogiroAmendmentResponseFile';

    /**
     * Layout for receiving rejected payments
     */
    const LAYOUT_PAYMENT_REJECTION = 'AutogiroPaymentRejectionFile';

    /**
     * Layout for receiving the current list of comming payments
     */
    const LAYOUT_PAYMENT_REPORT = 'AutogiroPaymentReportFile';
}
