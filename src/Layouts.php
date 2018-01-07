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
     * Layout for sending mandate requests
     */
    const LAYOUT_MANDATE_REQUEST = 'LAYOUT_MANDATE_REQUEST';

    /**
     * Layout for sending payment requests
     */
    const LAYOUT_PAYMENT_REQUEST = 'LAYOUT_PAYMENT_REQUEST';

    /**
     * Layout for sending payment amendments and revocations
     */
    const LAYOUT_AMENDMENT_REQUEST = 'LAYOUT_AMENDMENT_REQUEST';

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
     * Layout for receiving responses to previously made payment requests
     */
    const LAYOUT_PAYMENT_RESPONSE = 'LAYOUT_PAYMENT_RESPONSE';

    /**
     * Layout for receiving responses to previously made payment requests in BgMax format
     */
    const LAYOUT_PAYMENT_RESPONSE_BGMAX = 'LAYOUT_PAYMENT_RESPONSE_BGMAX';

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
