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
 * Copyright 2016 Hannes Forsgård
 */

namespace byrokrat\autogiro\Message;

/**
 * Collection of valid message ids
 */
interface Messages
{
    const MANDATE_REMOVED_BY_RECIPIENT                      = '73.03';
    const MANDATE_ADDED_BY_RECIPIENT                        = '73.04';
    const MANDATE_PAYER_NUMBER_CHANGED_BY_RECIPIENT         = '73.05';
    const MANDATE_REMOVED_DUE_TO_CLOSED_RECIPIENT_BG        = '73.10';
    const MANDATE_ACCOUNT_RESPONSE_FROM_BANK                = '73.42';
    const MANDATE_REMOVED_DUE_TO_UNANSWERED_ACCOUNT_REQUEST = '73.43';
    const MANDATE_REMOVED_DUE_TO_CLOSED_PAYER_BG            = '73.44';
    const MANDATE_REMOVED_BY_PAYER                          = '73.46';

    const MESSAGE_MAP = [
        self::MANDATE_REMOVED_BY_RECIPIENT                      => 'Makulering: Initierat av betalningsmottagaren.',
        self::MANDATE_ADDED_BY_RECIPIENT                        => 'Nyupplägg: Initierat av betalningsmottagaren.',
        self::MANDATE_PAYER_NUMBER_CHANGED_BY_RECIPIENT         => 'Byte av betalarnummer: Initierat av betalningsmottagaren.',
        self::MANDATE_REMOVED_DUE_TO_CLOSED_RECIPIENT_BG        => 'Makulerat på grund av att betalningsmottagarens bankgironummer är avslutat.',
        self::MANDATE_ACCOUNT_RESPONSE_FROM_BANK                => 'Svar på kontoförfrågan från bank på ny betalare i Autogiro.',
        self::MANDATE_REMOVED_DUE_TO_UNANSWERED_ACCOUNT_REQUEST => 'Makulerat/borttaget på grund av obesvarad kontoförfrågan.',
        self::MANDATE_REMOVED_DUE_TO_CLOSED_PAYER_BG            => 'Makulerat på grund av att betalarens bankgironummer är avslutat.',
        self::MANDATE_REMOVED_BY_PAYER                          => 'Makulering: Initierat av betalaren eller betalarens bank.',

        '73.comment.02' => 'Medgivandet är makulerat på initiativ av betalaren eller betalarens bank.',
        '73.comment.03' => 'Kontoslaget är inte godkänt för Autogiro.',
        '73.comment.04' => 'Medgivandet saknas i Bankgirots Medgivanderegister.',
        '73.comment.05' => 'Felaktiga bankkonto- eller personuppgifter.',
        '73.comment.07' => 'Makulerat/borttaget på grund av obesvarad kontoförfrågan.',
        '73.comment.09' => 'Betalarbankgironumret saknas hos Bankgirot.',
        '73.comment.10' => 'Medgivandet finns redan upplagt i Bankgirots register eller är under förfrågan.',
        '73.comment.20' => 'Felaktigt person-/organisationsnummer eller avtal om medgivande med bankgironummer saknas.',
        '73.comment.21' => 'Felaktigt betalarnummer.',
        '73.comment.23' => 'Felaktigt bankkontonummer.',
        '73.comment.29' => 'Mottagarbankgironummer är felaktigt.',
        '73.comment.30' => 'Mottagarbankgironummer är avregistrerat.',
        '73.comment.32' => 'Nytt Medgivande.',
        '73.comment.33' => 'Makulerad.',
        '73.comment.98' => 'Medgivandet är makulerat på grund av makulerat betalarbankgironummer.',
    ];

    // TODO lägga till även utdaterade meddelanden??
        // ja varför inte, så slipper vi störningar längre fram...
}
