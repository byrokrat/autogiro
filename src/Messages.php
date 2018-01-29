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

    const STATUS_PAYMENT_APPROVED                                   = Layouts::LAYOUT_PAYMENT_RESPONSE . '.0';
    const STATUS_PAYMENT_INSUFFICIENT_FUNDS                         = Layouts::LAYOUT_PAYMENT_RESPONSE . '.1';
    const STATUS_PAYMENT_DISAPPROVED                                = Layouts::LAYOUT_PAYMENT_RESPONSE . '.2';
    const STATUS_PAYMENT_RENEWED                                    = Layouts::LAYOUT_PAYMENT_RESPONSE . '.9';
    const STATUS_PAYMENT_MANDATE_MISSING                            = Layouts::LAYOUT_PAYMENT_RESPONSE . '.01';
    const STATUS_PAYMENT_MANDATE_REVOKED                            = Layouts::LAYOUT_PAYMENT_RESPONSE . '.02';
    const STATUS_PAYMENT_UNREASONABLE_AMOUNT                        = Layouts::LAYOUT_PAYMENT_RESPONSE . '.03';
    const STATUS_PAYMENT_APPROVED_OLD_FORMAT                        = Layouts::LAYOUT_PAYMENT_RESPONSE . '. ';

    const MESSAGE_MAP = [
        self::INFO_MANDATE_DELETED_BY_RECIPIENT
            => 'Makulering: Initierat av betalningsmottagaren.',
        self::INFO_MANDATE_CREATED_BY_RECIPIENT
            => 'Nyupplägg: Initierat av betalningsmottagaren.',
        self::INFO_MANDATE_UPDATED_PAYER_NUMBER_BY_RECIPIENT
            => 'Byte av betalarnummer: Initierat av betalningsmottagaren.',
        self::INFO_MANDATE_DELETED_DUE_TO_CLOSED_RECIPIENT_BG
            => 'Makulerat på grund av att betalningsmottagarens bankgironummer är avslutat.',
        self::INFO_MANDATE_ACCOUNT_RESPONSE_FROM_BANK
            => 'Svar på kontoförfrågan från bank på ny betalare i Autogiro.',
        self::INFO_MANDATE_DELETED_DUE_TO_UNANSWERED_ACCOUNT_REQUEST
            => 'Makulerat/borttaget på grund av obesvarad kontoförfrågan.',
        self::INFO_MANDATE_DELETED_DUE_TO_CLOSED_PAYER_BG
            => 'Makulerat på grund av att betalarens bankgironummer är avslutat.',
        self::INFO_MANDATE_DELETED_BY_PAYER_OR_BANK
            => 'Makulering: Initierat av betalaren eller betalarens bank.',
        self::INFO_MANDATE_DELETED_BY_PAYER
            => 'Makulering: Initierad av betalaren',

        self::STATUS_MANDATE_DELETED_BY_PAYER
            => 'Medgivandet är makulerat på initiativ av betalaren eller betalarens bank.',
        self::STATUS_MANDATE_ACCOUNT_NOT_ALLOWED
            => 'Kontoslaget är inte godkänt för Autogiro.',
        self::STATUS_MANDATE_DOES_NOT_EXIST
            => 'Medgivandet saknas i Bankgirots Medgivanderegister.',
        self::STATUS_MANDATE_INVALID_ACCOUNT_OR_ID
            => 'Felaktiga bankkonto- eller personuppgifter.',
        self::STATUS_MANDATE_DELETED_DUE_TO_UNANSWERED_ACCOUNT_REQUEST
            => 'Makulerat/borttaget på grund av obesvarad kontoförfrågan.',
        self::STATUS_MANDATE_PAYER_NUMBER_DOES_NOT_EXIST
            => 'Betalarbankgironumret saknas hos Bankgirot.',
        self::STATUS_MANDATE_ALREADY_EXISTS
            => 'Medgivandet finns redan upplagt i Bankgirots register eller är under förfrågan.',
        self::STATUS_MANDATE_INVALID_ID_OR_BG_NOT_ALLOWED
            => 'Felaktigt person-/organisationsnummer eller avtal om medgivande med bankgironummer saknas.',
        self::STATUS_MANDATE_INVALID_PAYER_NUMBER
            => 'Felaktigt betalarnummer.',
        self::STATUS_MANDATE_INVALID_ACCOUNT
            => 'Felaktigt bankkontonummer.',
        self::STATUS_MANDATE_INVALID_PAYEE_ACCOUNT
            => 'Mottagarbankgironummer är felaktigt.',
        self::STATUS_MANDATE_INACTIVE_PAYEE_ACCOUNT
            => 'Mottagarbankgironummer är avregistrerat.',
        self::STATUS_MANDATE_CREATED
            => 'Nytt Medgivande.',
        self::STATUS_MANDATE_DELETED
            => 'Makulerad.',
        self::STATUS_MANDATE_DELETED_DUE_TO_CLOSED_PAYER_BG
            => 'Medgivandet är makulerat på grund av makulerat betalarbankgironummer.',
        self::STATUS_MANDATE_DELETED_BY_BANK
            => 'Medgivandet makulerat på initiativ av banken.',
        self::STATUS_MANDATE_DELETED_BY_BGC
            => 'Medgivandet makulerat av Bankgirot.',
        self::STATUS_MANDATE_BLOCKED_BY_PAYER
            => 'Medgivandet stoppat av betalaren.',
        self::STATUS_MANDATE_BLOCK_REMOVED
            => 'Hävning av stopp av Medgivandet.',
        self::STATUS_MANDATE_MAX_AMOUNT_NOT_ALLOWED
            => 'Maxbelopp ej tillåtet',

        self::STATUS_PAYMENT_APPROVED
            => 'Godkänd betalning, betalningen är genomförd.',
        self::STATUS_PAYMENT_INSUFFICIENT_FUNDS
            => 'Täckning saknas, betalningen har inte genomförts.',
        self::STATUS_PAYMENT_DISAPPROVED
            => 'Koppling till Autogiro saknas (bankkontot avslutat), Annan orsak. Betalningen har inte genomförts.',
        self::STATUS_PAYMENT_RENEWED
            => 'Förnyad täckning, betalningen har inte genomförts men nytt försök ska göras om avtal finns.',
        self::STATUS_PAYMENT_MANDATE_MISSING
            => 'Medgivande har inte lämnats till betalningsmottagaren.',
        self::STATUS_PAYMENT_MANDATE_REVOKED
            => 'Medgivande har återkallats.',
        self::STATUS_PAYMENT_UNREASONABLE_AMOUNT
            => 'Beloppet angavs inte i samband med att Medgivandet tecknades
            och beloppet överstiger det betalaren rimligen kunde ha förväntat sig.',
        self::STATUS_PAYMENT_APPROVED_OLD_FORMAT
            => 'Godkänd betalning, betalningen är genomförd.',

        Layouts::LAYOUT_PAYMENT_REJECTION . '.01' => 'Utgår, Medgivande saknas.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.02' => 'Utgår, kontot är inte godkänt eller är avslutat.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.06' => 'Felaktig periodkod.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.07' => 'Felaktigt antal för Självförnyande uppdrag.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.08' => 'Belopp inte numeriskt.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.09' => 'Förbud mot utbetalningar.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.10' => 'Bankgironumret saknas hos Bankgirot.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.12' => 'Felaktigt betalningsdatum.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.13' => 'Passerat betalningsdatum.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.15'
            => 'Mottagarbankgironumret i öppningsposten och i transaktionsposten är inte detsamma.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.24' => 'Beloppet överstiger maxbeloppet.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.03' => 'Utgår, Medgivandet stoppat.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.04' => 'Felaktigt betalarbankgironummer.',
        Layouts::LAYOUT_PAYMENT_REJECTION . '.05' => 'Felaktigt mottagarbankgironummer.',
    ];
}
