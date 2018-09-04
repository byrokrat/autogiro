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
 * Copyright 2016-18 Hannes Forsgård
 */

namespace byrokrat\autogiro;

/**
 * Collection of valid message ids
 */
interface Messages
{
    const INFO_MANDATE_DELETED_BY_RECIPIENT                         = '73.info.03';
    const INFO_MANDATE_CREATED_BY_RECIPIENT                         = '73.info.04';
    const INFO_MANDATE_UPDATED_PAYER_NUMBER_BY_RECIPIENT            = '73.info.05';
    const INFO_MANDATE_DELETED_DUE_TO_CLOSED_RECIPIENT_BG           = '73.info.10';
    const INFO_MANDATE_ACCOUNT_RESPONSE_FROM_BANK                   = '73.info.42';
    const INFO_MANDATE_DELETED_DUE_TO_UNANSWERED_ACCOUNT_REQUEST    = '73.info.43';
    const INFO_MANDATE_DELETED_DUE_TO_CLOSED_PAYER_BG               = '73.info.44';
    const INFO_MANDATE_DELETED_BY_PAYER_OR_BANK                     = '73.info.46';
    const INFO_MANDATE_DELETED_BY_PAYER                             = '73.info.93';

    const STATUS_MANDATE_DELETED_BY_PAYER                           = '73.status.02';
    const STATUS_MANDATE_ACCOUNT_NOT_ALLOWED                        = '73.status.03';
    const STATUS_MANDATE_DOES_NOT_EXIST                             = '73.status.04';
    const STATUS_MANDATE_INVALID_ACCOUNT_OR_ID                      = '73.status.05';
    const STATUS_MANDATE_DELETED_DUE_TO_UNANSWERED_ACCOUNT_REQUEST  = '73.status.07';
    const STATUS_MANDATE_PAYER_NUMBER_DOES_NOT_EXIST                = '73.status.09';
    const STATUS_MANDATE_ALREADY_EXISTS                             = '73.status.10';
    const STATUS_MANDATE_INVALID_ID_OR_BG_NOT_ALLOWED               = '73.status.20';
    const STATUS_MANDATE_INVALID_PAYER_NUMBER                       = '73.status.21';
    const STATUS_MANDATE_INVALID_ACCOUNT                            = '73.status.23';
    const STATUS_MANDATE_INVALID_PAYEE_ACCOUNT                      = '73.status.29';
    const STATUS_MANDATE_INACTIVE_PAYEE_ACCOUNT                     = '73.status.30';
    const STATUS_MANDATE_CREATED                                    = '73.status.32';
    const STATUS_MANDATE_DELETED                                    = '73.status.33';
    const STATUS_MANDATE_DELETED_DUE_TO_CLOSED_PAYER_BG             = '73.status.98';
    const STATUS_MANDATE_DELETED_BY_BANK                            = '73.status.01';
    const STATUS_MANDATE_DELETED_BY_BGC                             = '73.status.06';
    const STATUS_MANDATE_BLOCKED_BY_PAYER                           = '73.status.11';
    const STATUS_MANDATE_BLOCK_REMOVED                              = '73.status.12';
    const STATUS_MANDATE_MAX_AMOUNT_NOT_ALLOWED                     = '73.status.24';

    const STATUS_PAYMENT_APPROVED                                   = '.0';
    const STATUS_PAYMENT_INSUFFICIENT_FUNDS                         = '.1';
    const STATUS_PAYMENT_DISAPPROVED                                = '.2';
    const STATUS_PAYMENT_RENEWED                                    = '.9';
    const STATUS_PAYMENT_MANDATE_MISSING                            = '.01';
    const STATUS_PAYMENT_MANDATE_REVOKED                            = '.02';
    const STATUS_PAYMENT_UNREASONABLE_AMOUNT                        = '.03';
    const STATUS_PAYMENT_APPROVED_OLD_FORMAT                        = '. ';
}
