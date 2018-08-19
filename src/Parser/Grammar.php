<?php

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Layouts;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\BgcNumberNode;
use byrokrat\autogiro\Tree\ImmediateDateNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\DateTimeNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\ReferredAccountNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\Request;
use byrokrat\autogiro\Tree\Response;
use byrokrat\autogiro\Tree\TextNode;

class Grammar extends MultibyteHack
{
    protected function parseFILE()
    {
        $_position = $this->position;

        if (isset($this->cache['FILE'][$_position])) {
            $_success = $this->cache['FILE'][$_position]['success'];
            $this->position = $this->cache['FILE'][$_position]['position'];
            $this->value = $this->cache['FILE'][$_position]['value'];

            return $_success;
        }

        $_value3 = array();

        $_success = $this->parseRESET_LINE_COUNT();

        if ($_success) {
            $_value3[] = $this->value;

            $_position1 = $this->position;
            $_cut2 = $this->cut;

            $this->cut = false;
            $_success = $this->parseREQUEST_FILE();

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parseMANDATE_FILE();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parsePAYMENT_FILE();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parsePAYMENT_REJECTION_FILE();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parseAMENDMENT_FILE();
            }

            $this->cut = $_cut2;

            if ($_success) {
                $file = $this->value;
            }
        }

        if ($_success) {
            $_value3[] = $this->value;

            $_success = $this->parseVOID();
        }

        if ($_success) {
            $_value3[] = $this->value;

            $this->value = $_value3;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$file) {
                return $file;
            });
        }

        $this->cache['FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'FILE');
        }

        return $_success;
    }

    protected function parseRESET_LINE_COUNT()
    {
        $_position = $this->position;

        if (isset($this->cache['RESET_LINE_COUNT'][$_position])) {
            $_success = $this->cache['RESET_LINE_COUNT'][$_position]['success'];
            $this->position = $this->cache['RESET_LINE_COUNT'][$_position]['position'];
            $this->value = $this->cache['RESET_LINE_COUNT'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen('')) === '') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen(''));
            $this->position += strlen('');
        } else {
            $_success = false;

            $this->report($this->position, '\'\'');
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                $this->lineNr = 0;
            });
        }

        $this->cache['RESET_LINE_COUNT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESET_LINE_COUNT');
        }

        return $_success;
    }

    protected function parseREQUEST_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['REQUEST_FILE'][$_position])) {
            $_success = $this->cache['REQUEST_FILE'][$_position]['success'];
            $this->position = $this->cache['REQUEST_FILE'][$_position]['position'];
            $this->value = $this->cache['REQUEST_FILE'][$_position]['value'];

            return $_success;
        }

        $_position4 = $this->position;
        $_cut5 = $this->cut;

        $this->cut = false;
        $_success = $this->parseREQ_MANDATE_SECTION();

        if (!$_success && !$this->cut) {
            $this->position = $_position4;

            $_success = $this->parseREQ_PAYMENT_SECTION();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position4;

            $_success = $this->parseREQ_AMENDMENT_SECTION();
        }

        $this->cut = $_cut5;

        if ($_success) {
            $_value7 = array($this->value);
            $_cut8 = $this->cut;

            while (true) {
                $_position6 = $this->position;

                $this->cut = false;
                $_position4 = $this->position;
                $_cut5 = $this->cut;

                $this->cut = false;
                $_success = $this->parseREQ_MANDATE_SECTION();

                if (!$_success && !$this->cut) {
                    $this->position = $_position4;

                    $_success = $this->parseREQ_PAYMENT_SECTION();
                }

                if (!$_success && !$this->cut) {
                    $this->position = $_position4;

                    $_success = $this->parseREQ_AMENDMENT_SECTION();
                }

                $this->cut = $_cut5;

                if (!$_success) {
                    break;
                }

                $_value7[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position6;
                $this->value = $_value7;
            }

            $this->cut = $_cut8;
        }

        if ($_success) {
            $sections = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$sections) {
                return new FileNode(Layouts::LAYOUT_REQUEST, ...$sections);
            });
        }

        $this->cache['REQUEST_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQUEST_FILE');
        }

        return $_success;
    }

    protected function parseREQ_OPENING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_OPENING_REC'][$_position])) {
            $_success = $this->cache['REQ_OPENING_REC'][$_position]['success'];
            $this->position = $this->cache['REQ_OPENING_REC'][$_position]['position'];
            $this->value = $this->cache['REQ_OPENING_REC'][$_position]['value'];

            return $_success;
        }

        $_value9 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value9[] = $this->value;

            if (substr($this->string, $this->position, strlen('AUTOGIRO')) === 'AUTOGIRO') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO'));
                $this->position += strlen('AUTOGIRO');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO\'');
            }
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value9[] = $this->value;

            $this->value = $_value9;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Request\RequestOpening(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'payee_bgc_number' => $bgcNr,
                        'payee_bankgiro' => $bg
                    ]
                );
            });
        }

        $this->cache['REQ_OPENING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_OPENING_REC');
        }

        return $_success;
    }

    protected function parseREQ_MANDATE_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_MANDATE_SECTION'][$_position])) {
            $_success = $this->cache['REQ_MANDATE_SECTION'][$_position]['success'];
            $this->position = $this->cache['REQ_MANDATE_SECTION'][$_position]['position'];
            $this->value = $this->cache['REQ_MANDATE_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value15 = array();

        $_success = $this->parseREQ_OPENING_REC();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value15[] = $this->value;

            $_position10 = $this->position;
            $_cut11 = $this->cut;

            $this->cut = false;
            $_success = $this->parseREQ_DEL_MANDATE_REC();

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_REJECT_MANDATE_REC();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_CREATE_MANDATE_REC();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_UPDATE_MANDATE_REC();
            }

            $this->cut = $_cut11;

            if ($_success) {
                $_value13 = array($this->value);
                $_cut14 = $this->cut;

                while (true) {
                    $_position12 = $this->position;

                    $this->cut = false;
                    $_position10 = $this->position;
                    $_cut11 = $this->cut;

                    $this->cut = false;
                    $_success = $this->parseREQ_DEL_MANDATE_REC();

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_REJECT_MANDATE_REC();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_CREATE_MANDATE_REC();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_UPDATE_MANDATE_REC();
                    }

                    $this->cut = $_cut11;

                    if (!$_success) {
                        break;
                    }

                    $_value13[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position12;
                    $this->value = $_value13;
                }

                $this->cut = $_cut14;
            }

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value15[] = $this->value;

            $this->value = $_value15;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Request\MandateRequestSection($open, ...$records);
            });
        }

        $this->cache['REQ_MANDATE_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_MANDATE_SECTION');
        }

        return $_success;
    }

    protected function parseREQ_DEL_MANDATE_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_DEL_MANDATE_REC'][$_position])) {
            $_success = $this->cache['REQ_DEL_MANDATE_REC'][$_position]['success'];
            $this->position = $this->cache['REQ_DEL_MANDATE_REC'][$_position]['position'];
            $this->value = $this->cache['REQ_DEL_MANDATE_REC'][$_position]['value'];

            return $_success;
        }

        $_value16 = array();

        if (substr($this->string, $this->position, strlen('03')) === '03') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('03'));
            $this->position += strlen('03');
        } else {
            $_success = false;

            $this->report($this->position, '\'03\'');
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value16[] = $this->value;

            $this->value = $_value16;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr) {
                return new Request\DeleteMandateRequest($this->lineNr, ['payee_bankgiro' => $bg, 'payer_number' => $payerNr]);
            });
        }

        $this->cache['REQ_DEL_MANDATE_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_DEL_MANDATE_REC');
        }

        return $_success;
    }

    protected function parseREQ_REJECT_MANDATE_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_REJECT_MANDATE_REC'][$_position])) {
            $_success = $this->cache['REQ_REJECT_MANDATE_REC'][$_position]['success'];
            $this->position = $this->cache['REQ_REJECT_MANDATE_REC'][$_position]['position'];
            $this->value = $this->cache['REQ_REJECT_MANDATE_REC'][$_position]['value'];

            return $_success;
        }

        $_value17 = array();

        if (substr($this->string, $this->position, strlen('04')) === '04') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('04'));
            $this->position += strlen('04');
        } else {
            $_success = false;

            $this->report($this->position, '\'04\'');
        }

        if ($_success) {
            $_value17[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value17[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value17[] = $this->value;

            $_success = $this->parseTXT48();
        }

        if ($_success) {
            $_value17[] = $this->value;

            if (substr($this->string, $this->position, strlen('AV')) === 'AV') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AV'));
                $this->position += strlen('AV');
            } else {
                $_success = false;

                $this->report($this->position, '\'AV\'');
            }
        }

        if ($_success) {
            $_value17[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value17[] = $this->value;

            $this->value = $_value17;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr) {
                return new Request\RejectDigitalMandateRequest($this->lineNr, ['payee_bankgiro' => $bg, 'payer_number' => $payerNr]);
            });
        }

        $this->cache['REQ_REJECT_MANDATE_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_REJECT_MANDATE_REC');
        }

        return $_success;
    }

    protected function parseREQ_CREATE_MANDATE_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_CREATE_MANDATE_REC'][$_position])) {
            $_success = $this->cache['REQ_CREATE_MANDATE_REC'][$_position]['success'];
            $this->position = $this->cache['REQ_CREATE_MANDATE_REC'][$_position]['position'];
            $this->value = $this->cache['REQ_CREATE_MANDATE_REC'][$_position]['value'];

            return $_success;
        }

        $_value22 = array();

        if (substr($this->string, $this->position, strlen('04')) === '04') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('04'));
            $this->position += strlen('04');
        } else {
            $_success = false;

            $this->report($this->position, '\'04\'');
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_position18 = $this->position;
            $_cut19 = $this->cut;

            $this->cut = false;
            $_success = $this->parseACCOUNT16();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position18;
                $this->value = null;
            }

            $this->cut = $_cut19;

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_position20 = $this->position;
            $_cut21 = $this->cut;

            $this->cut = false;
            $_success = $this->parseID();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position20;
                $this->value = null;
            }

            $this->cut = $_cut21;

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $this->value = $_value22;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$account, &$id) {
                return $id && trim($id->getValue())
                    ? new Request\CreateMandateRequest(
                        $this->lineNr,
                        [
                            'payee_bankgiro' => $bg,
                            'payer_number' => $payerNr,
                            'account' => $account,
                            'id' => $id
                        ]
                    )
                    : new Request\AcceptDigitalMandateRequest(
                        $this->lineNr,
                        [
                            'payee_bankgiro' => $bg,
                            'payer_number' => $payerNr
                        ]
                    );
            });
        }

        $this->cache['REQ_CREATE_MANDATE_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_CREATE_MANDATE_REC');
        }

        return $_success;
    }

    protected function parseREQ_UPDATE_MANDATE_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_UPDATE_MANDATE_REC'][$_position])) {
            $_success = $this->cache['REQ_UPDATE_MANDATE_REC'][$_position]['success'];
            $this->position = $this->cache['REQ_UPDATE_MANDATE_REC'][$_position]['position'];
            $this->value = $this->cache['REQ_UPDATE_MANDATE_REC'][$_position]['value'];

            return $_success;
        }

        $_value23 = array();

        if (substr($this->string, $this->position, strlen('05')) === '05') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('05'));
            $this->position += strlen('05');
        } else {
            $_success = false;

            $this->report($this->position, '\'05\'');
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $oldBg = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $oldPayerNr = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $newBg = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $newPayerNr = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value23[] = $this->value;

            $this->value = $_value23;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$oldBg, &$oldPayerNr, &$newBg, &$newPayerNr) {
                return new Request\UpdateMandateRequest(
                    $this->lineNr,
                    [
                        'payee_bankgiro' => $oldBg,
                        'payer_number' => $oldPayerNr,
                        'new_payee_bankgiro' => $newBg,
                        'new_payer_number' => $newPayerNr
                    ]
                );
            });
        }

        $this->cache['REQ_UPDATE_MANDATE_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_UPDATE_MANDATE_REC');
        }

        return $_success;
    }

    protected function parseREQ_PAYMENT_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_PAYMENT_SECTION'][$_position])) {
            $_success = $this->cache['REQ_PAYMENT_SECTION'][$_position]['success'];
            $this->position = $this->cache['REQ_PAYMENT_SECTION'][$_position]['position'];
            $this->value = $this->cache['REQ_PAYMENT_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value27 = array();

        $_success = $this->parseREQ_OPENING_REC();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parseREQ_PAYMENT_REC();

            if ($_success) {
                $_value25 = array($this->value);
                $_cut26 = $this->cut;

                while (true) {
                    $_position24 = $this->position;

                    $this->cut = false;
                    $_success = $this->parseREQ_PAYMENT_REC();

                    if (!$_success) {
                        break;
                    }

                    $_value25[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position24;
                    $this->value = $_value25;
                }

                $this->cut = $_cut26;
            }

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value27[] = $this->value;

            $this->value = $_value27;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Request\PaymentRequestSection($open, ...$records);
            });
        }

        $this->cache['REQ_PAYMENT_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_PAYMENT_SECTION');
        }

        return $_success;
    }

    protected function parseREQ_PAYMENT_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_PAYMENT_REC'][$_position])) {
            $_success = $this->cache['REQ_PAYMENT_REC'][$_position]['success'];
            $this->position = $this->cache['REQ_PAYMENT_REC'][$_position]['position'];
            $this->value = $this->cache['REQ_PAYMENT_REC'][$_position]['value'];

            return $_success;
        }

        $_value32 = array();

        $_position28 = $this->position;
        $_cut29 = $this->cut;

        $this->cut = false;
        $_success = $this->parseREQ_PAYMENT_INCOMING();

        if (!$_success && !$this->cut) {
            $this->position = $_position28;

            $_success = $this->parseREQ_PAYMENT_OUTGOING();
        }

        $this->cut = $_cut29;

        if ($_success) {
            $type = $this->value;
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_position30 = $this->position;
            $_cut31 = $this->cut;

            $this->cut = false;
            $_success = $this->parseIMMEDIATE_DATE();

            if (!$_success && !$this->cut) {
                $this->position = $_position30;

                $_success = $this->parseDATE();
            }

            $this->cut = $_cut31;

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            if (substr($this->string, $this->position, strlen(' ')) === ' ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen(' '));
                $this->position += strlen(' ');
            } else {
                $_success = false;

                $this->report($this->position, '\' \'');
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseVAR_TXT();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value32[] = $this->value;

            $this->value = $_value32;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$type, &$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref) {
                return new $type(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'interval' => $ival,
                        'repetitions' => $reps,
                        'payer_number' => $payerNr,
                        'amount' => $amount,
                        'payee_bankgiro' => $bg,
                        'reference' => $ref
                    ]
                );
            });
        }

        $this->cache['REQ_PAYMENT_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_PAYMENT_REC');
        }

        return $_success;
    }

    protected function parseREQ_PAYMENT_INCOMING()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_PAYMENT_INCOMING'][$_position])) {
            $_success = $this->cache['REQ_PAYMENT_INCOMING'][$_position]['success'];
            $this->position = $this->cache['REQ_PAYMENT_INCOMING'][$_position]['position'];
            $this->value = $this->cache['REQ_PAYMENT_INCOMING'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen('82')) === '82') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('82'));
            $this->position += strlen('82');
        } else {
            $_success = false;

            $this->report($this->position, '\'82\'');
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return Request\IncomingPaymentRequest::CLASS;
            });
        }

        $this->cache['REQ_PAYMENT_INCOMING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_PAYMENT_INCOMING');
        }

        return $_success;
    }

    protected function parseREQ_PAYMENT_OUTGOING()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_PAYMENT_OUTGOING'][$_position])) {
            $_success = $this->cache['REQ_PAYMENT_OUTGOING'][$_position]['success'];
            $this->position = $this->cache['REQ_PAYMENT_OUTGOING'][$_position]['position'];
            $this->value = $this->cache['REQ_PAYMENT_OUTGOING'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen('32')) === '32') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('32'));
            $this->position += strlen('32');
        } else {
            $_success = false;

            $this->report($this->position, '\'32\'');
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return Request\OutgoingPaymentRequest::CLASS;
            });
        }

        $this->cache['REQ_PAYMENT_OUTGOING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_PAYMENT_OUTGOING');
        }

        return $_success;
    }

    protected function parseREQ_AMENDMENT_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_AMENDMENT_SECTION'][$_position])) {
            $_success = $this->cache['REQ_AMENDMENT_SECTION'][$_position]['success'];
            $this->position = $this->cache['REQ_AMENDMENT_SECTION'][$_position]['position'];
            $this->value = $this->cache['REQ_AMENDMENT_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value36 = array();

        $_success = $this->parseREQ_OPENING_REC();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value36[] = $this->value;

            if (substr($this->string, $this->position, strlen('TODO')) === 'TODO') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('TODO'));
                $this->position += strlen('TODO');
            } else {
                $_success = false;

                $this->report($this->position, '\'TODO\'');
            }

            if ($_success) {
                $_value34 = array($this->value);
                $_cut35 = $this->cut;

                while (true) {
                    $_position33 = $this->position;

                    $this->cut = false;
                    if (substr($this->string, $this->position, strlen('TODO')) === 'TODO') {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, strlen('TODO'));
                        $this->position += strlen('TODO');
                    } else {
                        $_success = false;

                        $this->report($this->position, '\'TODO\'');
                    }

                    if (!$_success) {
                        break;
                    }

                    $_value34[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position33;
                    $this->value = $_value34;
                }

                $this->cut = $_cut35;
            }

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $this->value = $_value36;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Request\AmendmentRequestSection($open, ...$records);
            });
        }

        $this->cache['REQ_AMENDMENT_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_AMENDMENT_SECTION');
        }

        return $_success;
    }

    protected function parsePAYMENT_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_FILE'][$_position])) {
            $_success = $this->cache['PAYMENT_FILE'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_FILE'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_FILE'][$_position]['value'];

            return $_success;
        }

        $_position37 = $this->position;
        $_cut38 = $this->cut;

        $this->cut = false;
        $_success = $this->parseNEW_PAYMENT_FILE();

        if (!$_success && !$this->cut) {
            $this->position = $_position37;

            $_success = $this->parseOLD_PAYMENT_FILE();
        }

        $this->cut = $_cut38;

        $this->cache['PAYMENT_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_FILE');
        }

        return $_success;
    }

    protected function parseNEW_PAYMENT_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['NEW_PAYMENT_FILE'][$_position])) {
            $_success = $this->cache['NEW_PAYMENT_FILE'][$_position]['success'];
            $this->position = $this->cache['NEW_PAYMENT_FILE'][$_position]['position'];
            $this->value = $this->cache['NEW_PAYMENT_FILE'][$_position]['value'];

            return $_success;
        }

        $_value44 = array();

        $_success = $this->parsePAYMENT_OPENING_REC();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_position39 = $this->position;
            $_cut40 = $this->cut;

            $this->cut = false;
            $_success = $this->parsePAYMENT_INCOMING_SECTION();

            if (!$_success && !$this->cut) {
                $this->position = $_position39;

                $_success = $this->parsePAYMENT_OUTGOING_SECTION();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position39;

                $_success = $this->parsePAYMENT_REFUND_SECTION();
            }

            $this->cut = $_cut40;

            if ($_success) {
                $_value42 = array($this->value);
                $_cut43 = $this->cut;

                while (true) {
                    $_position41 = $this->position;

                    $this->cut = false;
                    $_position39 = $this->position;
                    $_cut40 = $this->cut;

                    $this->cut = false;
                    $_success = $this->parsePAYMENT_INCOMING_SECTION();

                    if (!$_success && !$this->cut) {
                        $this->position = $_position39;

                        $_success = $this->parsePAYMENT_OUTGOING_SECTION();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position39;

                        $_success = $this->parsePAYMENT_REFUND_SECTION();
                    }

                    $this->cut = $_cut40;

                    if (!$_success) {
                        break;
                    }

                    $_value42[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position41;
                    $this->value = $_value42;
                }

                $this->cut = $_cut43;
            }

            if ($_success) {
                $sections = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parsePAYMENT_CLOSING_REC();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $this->value = $_value44;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$sections, &$close) {
                $sections[] = $close;
                return new FileNode(Layouts::LAYOUT_PAYMENT_RESPONSE, $open, ...$sections);
            });
        }

        $this->cache['NEW_PAYMENT_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'NEW_PAYMENT_FILE');
        }

        return $_success;
    }

    protected function parsePAYMENT_OPENING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_OPENING_REC'][$_position])) {
            $_success = $this->cache['PAYMENT_OPENING_REC'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_OPENING_REC'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_OPENING_REC'][$_position]['value'];

            return $_success;
        }

        $_value45 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value45[] = $this->value;

            if (substr($this->string, $this->position, strlen('AUTOGIRO')) === 'AUTOGIRO') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO'));
                $this->position += strlen('AUTOGIRO');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO\'');
            }
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = $this->parseDATETIME();

            if ($_success) {
                $datetime = $this->value;
            }
        }

        if ($_success) {
            $_value45[] = $this->value;

            if (substr($this->string, $this->position, strlen('BET. SPEC & STOPP TK')) === 'BET. SPEC & STOPP TK') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('BET. SPEC & STOPP TK'));
                $this->position += strlen('BET. SPEC & STOPP TK');
            } else {
                $_success = false;

                $this->report($this->position, '\'BET. SPEC & STOPP TK\'');
            }
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value45[] = $this->value;

            $this->value = $_value45;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$datetime, &$bgcNr, &$bg) {
                return new Response\ResponseOpening(
                    $this->lineNr,
                    [
                        'date' => $datetime,
                        'payee_bgc_number' => $bgcNr,
                        'payee_bankgiro' => $bg,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_OPENING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_OPENING_REC');
        }

        return $_success;
    }

    protected function parsePAYMENT_CLOSING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_CLOSING_REC'][$_position])) {
            $_success = $this->cache['PAYMENT_CLOSING_REC'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_CLOSING_REC'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_CLOSING_REC'][$_position]['value'];

            return $_success;
        }

        $_value46 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900')) === '9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900'));
                $this->position += strlen('9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900\'');
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrInSecs = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrInRecs = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOutSecs = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrOutRecs = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrRefSecs = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrRefRecs = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value46[] = $this->value;

            $this->value = $_value46;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrInSecs, &$nrInRecs, &$nrOutSecs, &$nrOutRecs, &$nrRefSecs, &$nrRefRecs) {
                return new Response\PaymentResponseClosing(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'nr_of_incoming_sections' => $nrInSecs,
                        'nr_of_incoming_records' => $nrInRecs,
                        'nr_of_outgoing_sections' => $nrOutSecs,
                        'nr_of_outgoing_records' => $nrOutRecs,
                        'nr_of_refund_sections' => $nrRefSecs,
                        'nr_of_refund_records' => $nrRefRecs,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_CLOSING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_CLOSING_REC');
        }

        return $_success;
    }

    protected function parsePAYMENT_INCOMING_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_INCOMING_SECTION'][$_position])) {
            $_success = $this->cache['PAYMENT_INCOMING_SECTION'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_INCOMING_SECTION'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_INCOMING_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value50 = array();

        $_success = $this->parsePAYMENT_INCOMING_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_value48 = array();
            $_cut49 = $this->cut;

            while (true) {
                $_position47 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_INCOMING_REC();

                if (!$_success) {
                    break;
                }

                $_value48[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position47;
                $this->value = $_value48;
            }

            $this->cut = $_cut49;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $this->value = $_value50;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Response\IncomingPaymentResponseSection($open, ...$records);
            });
        }

        $this->cache['PAYMENT_INCOMING_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_INCOMING_SECTION');
        }

        return $_success;
    }

    protected function parsePAYMENT_INCOMING_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_INCOMING_OPENING'][$_position])) {
            $_success = $this->cache['PAYMENT_INCOMING_OPENING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_INCOMING_OPENING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_INCOMING_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value51 = array();

        if (substr($this->string, $this->position, strlen('15')) === '15') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('15'));
            $this->position += strlen('15');
        } else {
            $_success = false;

            $this->report($this->position, '\'15\'');
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value51[] = $this->value;

            $this->value = $_value51;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$account, &$date, &$serial, &$amount, &$nrRecs) {
                return new Response\IncomingPaymentResponseOpening(
                    $this->lineNr,
                    [
                        'account' => $account,
                        'date' => $date,
                        'serial' => $serial,
                        'amount' => $amount,
                        'record_count' => $nrRecs,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_INCOMING_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_INCOMING_OPENING');
        }

        return $_success;
    }

    protected function parsePAYMENT_INCOMING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_INCOMING_REC'][$_position])) {
            $_success = $this->cache['PAYMENT_INCOMING_REC'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_INCOMING_REC'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_INCOMING_REC'][$_position]['value'];

            return $_success;
        }

        $_value56 = array();

        if (substr($this->string, $this->position, strlen('82')) === '82') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('82'));
            $this->position += strlen('82');
        } else {
            $_success = false;

            $this->report($this->position, '\'82\'');
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_position52 = $this->position;
            $_cut53 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA10();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position52;
                $this->value = null;
            }

            $this->cut = $_cut53;
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_position54 = $this->position;
            $_cut55 = $this->cut;

            $this->cut = false;
            $_success = $this->parseMSG1();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position54;
                $this->value = null;
            }

            $this->cut = $_cut55;

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value56[] = $this->value;

            $this->value = $_value56;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                $data = [
                    'date' => $date,
                    'interval' => $ival,
                    'repetitions' => $reps,
                    'payer_number' => $payerNr,
                    'amount' => $amount,
                    'payee_bankgiro' => $bg,
                    'reference' => $ref,
                ];

                if ($status) {
                    $status->setAttribute('message_id', Layouts::LAYOUT_PAYMENT_RESPONSE . '.' . $status->getValue());
                    $data['status'] = $status;
                }

                return new Response\IncomingPaymentResponse($this->lineNr, $data);
            });
        }

        $this->cache['PAYMENT_INCOMING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_INCOMING_REC');
        }

        return $_success;
    }

    protected function parsePAYMENT_OUTGOING_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_OUTGOING_SECTION'][$_position])) {
            $_success = $this->cache['PAYMENT_OUTGOING_SECTION'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_OUTGOING_SECTION'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_OUTGOING_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value60 = array();

        $_success = $this->parsePAYMENT_OUTGOING_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_value58 = array();
            $_cut59 = $this->cut;

            while (true) {
                $_position57 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_OUTGOING_REC();

                if (!$_success) {
                    break;
                }

                $_value58[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position57;
                $this->value = $_value58;
            }

            $this->cut = $_cut59;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $this->value = $_value60;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Response\OutgoingPaymentResponseSection($open, ...$records);
            });
        }

        $this->cache['PAYMENT_OUTGOING_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_OUTGOING_SECTION');
        }

        return $_success;
    }

    protected function parsePAYMENT_OUTGOING_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_OUTGOING_OPENING'][$_position])) {
            $_success = $this->cache['PAYMENT_OUTGOING_OPENING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_OUTGOING_OPENING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_OUTGOING_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value61 = array();

        if (substr($this->string, $this->position, strlen('16')) === '16') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('16'));
            $this->position += strlen('16');
        } else {
            $_success = false;

            $this->report($this->position, '\'16\'');
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value61[] = $this->value;

            $this->value = $_value61;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$account, &$date, &$serial, &$amount, &$nrRecs) {
                return new Response\OutgoingPaymentResponseOpening(
                    $this->lineNr,
                    [
                        'account' => $account,
                        'date' => $date,
                        'serial' => $serial,
                        'amount' => $amount,
                        'record_count' => $nrRecs,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_OUTGOING_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_OUTGOING_OPENING');
        }

        return $_success;
    }

    protected function parsePAYMENT_OUTGOING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_OUTGOING_REC'][$_position])) {
            $_success = $this->cache['PAYMENT_OUTGOING_REC'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_OUTGOING_REC'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_OUTGOING_REC'][$_position]['value'];

            return $_success;
        }

        $_value66 = array();

        if (substr($this->string, $this->position, strlen('32')) === '32') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('32'));
            $this->position += strlen('32');
        } else {
            $_success = false;

            $this->report($this->position, '\'32\'');
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_position62 = $this->position;
            $_cut63 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA10();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position62;
                $this->value = null;
            }

            $this->cut = $_cut63;
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_position64 = $this->position;
            $_cut65 = $this->cut;

            $this->cut = false;
            $_success = $this->parseMSG1();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position64;
                $this->value = null;
            }

            $this->cut = $_cut65;

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value66[] = $this->value;

            $this->value = $_value66;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                $data = [
                    'date' => $date,
                    'interval' => $ival,
                    'repetitions' => $reps,
                    'payer_number' => $payerNr,
                    'amount' => $amount,
                    'payee_bankgiro' => $bg,
                    'reference' => $ref,
                ];

                if ($status) {
                    $status->setAttribute('message_id', Layouts::LAYOUT_PAYMENT_RESPONSE . '.' . $status->getValue());
                    $data['status'] = $status;
                }

                return new Response\OutgoingPaymentResponse($this->lineNr, $data);
            });
        }

        $this->cache['PAYMENT_OUTGOING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_OUTGOING_REC');
        }

        return $_success;
    }

    protected function parsePAYMENT_REFUND_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REFUND_SECTION'][$_position])) {
            $_success = $this->cache['PAYMENT_REFUND_SECTION'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REFUND_SECTION'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REFUND_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value70 = array();

        $_success = $this->parsePAYMENT_REFUND_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_value68 = array();
            $_cut69 = $this->cut;

            while (true) {
                $_position67 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_REFUND_REC();

                if (!$_success) {
                    break;
                }

                $_value68[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position67;
                $this->value = $_value68;
            }

            $this->cut = $_cut69;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value70[] = $this->value;

            $this->value = $_value70;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Response\RefundPaymentResponseSection($open, ...$records);
            });
        }

        $this->cache['PAYMENT_REFUND_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REFUND_SECTION');
        }

        return $_success;
    }

    protected function parsePAYMENT_REFUND_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REFUND_OPENING'][$_position])) {
            $_success = $this->cache['PAYMENT_REFUND_OPENING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REFUND_OPENING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REFUND_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value71 = array();

        if (substr($this->string, $this->position, strlen('17')) === '17') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('17'));
            $this->position += strlen('17');
        } else {
            $_success = false;

            $this->report($this->position, '\'17\'');
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value71[] = $this->value;

            $this->value = $_value71;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$account, &$date, &$serial, &$amount, &$nrRecs) {
                return new Response\RefundPaymentResponseOpening(
                    $this->lineNr,
                    [
                        'account' => $account,
                        'date' => $date,
                        'serial' => $serial,
                        'amount' => $amount,
                        'record_count' => $nrRecs,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_REFUND_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REFUND_OPENING');
        }

        return $_success;
    }

    protected function parsePAYMENT_REFUND_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REFUND_REC'][$_position])) {
            $_success = $this->cache['PAYMENT_REFUND_REC'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REFUND_REC'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REFUND_REC'][$_position]['value'];

            return $_success;
        }

        $_value72 = array();

        if (substr($this->string, $this->position, strlen('77')) === '77') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('77'));
            $this->position += strlen('77');
        } else {
            $_success = false;

            $this->report($this->position, '\'77\'');
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $refundDate = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value72[] = $this->value;

            $this->value = $_value72;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$refundDate, &$status) {
                $status->setAttribute('message_id', Layouts::LAYOUT_PAYMENT_RESPONSE . '.' . $status->getValue());
                return new Response\RefundPaymentResponse(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'interval' => $ival,
                        'repetitions' => $reps,
                        'payer_number' => $payerNr,
                        'amount' => $amount,
                        'payee_bankgiro' => $bg,
                        'reference' => $ref,
                        'refund_date' => $refundDate,
                        'status' => $status,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_REFUND_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REFUND_REC');
        }

        return $_success;
    }

    protected function parseOLD_PAYMENT_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_PAYMENT_FILE'][$_position])) {
            $_success = $this->cache['OLD_PAYMENT_FILE'][$_position]['success'];
            $this->position = $this->cache['OLD_PAYMENT_FILE'][$_position]['position'];
            $this->value = $this->cache['OLD_PAYMENT_FILE'][$_position]['value'];

            return $_success;
        }

        $_value78 = array();

        $_success = $this->parseOLD_PAYMENT_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_value76 = array();
            $_cut77 = $this->cut;

            while (true) {
                $_position75 = $this->position;

                $this->cut = false;
                $_position73 = $this->position;
                $_cut74 = $this->cut;

                $this->cut = false;
                $_success = $this->parsePAYMENT_INCOMING_REC();

                if (!$_success && !$this->cut) {
                    $this->position = $_position73;

                    $_success = $this->parsePAYMENT_OUTGOING_REC();
                }

                $this->cut = $_cut74;

                if (!$_success) {
                    break;
                }

                $_value76[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position75;
                $this->value = $_value76;
            }

            $this->cut = $_cut77;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseOLD_PAYMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $this->value = $_value78;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$recs, &$close) {
                $recs[] = $close;
                return new FileNode(Layouts::LAYOUT_PAYMENT_RESPONSE_OLD, $open, ...$recs);
            });
        }

        $this->cache['OLD_PAYMENT_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_PAYMENT_FILE');
        }

        return $_success;
    }

    protected function parseOLD_PAYMENT_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_PAYMENT_OPENING'][$_position])) {
            $_success = $this->cache['OLD_PAYMENT_OPENING'][$_position]['success'];
            $this->position = $this->cache['OLD_PAYMENT_OPENING'][$_position]['position'];
            $this->value = $this->cache['OLD_PAYMENT_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value79 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            if (substr($this->string, $this->position, strlen('AUTOGIRO')) === 'AUTOGIRO') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO'));
                $this->position += strlen('AUTOGIRO');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO\'');
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900')) === '9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900'));
                $this->position += strlen('9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900\'');
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $this->value = $_value79;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Response\ResponseOpening(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'payee_bgc_number' => $bgcNr,
                        'payee_bankgiro' => $bg,
                    ]
                );
            });
        }

        $this->cache['OLD_PAYMENT_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_PAYMENT_OPENING');
        }

        return $_success;
    }

    protected function parseOLD_PAYMENT_CLOSING()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_PAYMENT_CLOSING'][$_position])) {
            $_success = $this->cache['OLD_PAYMENT_CLOSING'][$_position]['success'];
            $this->position = $this->cache['OLD_PAYMENT_CLOSING'][$_position]['position'];
            $this->value = $this->cache['OLD_PAYMENT_CLOSING'][$_position]['value'];

            return $_success;
        }

        $_value80 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900')) === '9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900'));
                $this->position += strlen('9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900\'');
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

            if (substr($this->string, $this->position, strlen('00000')) === '00000') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('00000'));
                $this->position += strlen('00000');
            } else {
                $_success = false;

                $this->report($this->position, '\'00000\'');
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

            if (substr($this->string, $this->position, strlen('00000000000')) === '00000000000') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('00000000000'));
                $this->position += strlen('00000000000');
            } else {
                $_success = false;

                $this->report($this->position, '\'00000000000\'');
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $this->value = $_value80;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$amountOut, &$nrOut, &$nrIn, &$amountIn) {
                return new Response\PaymentResponseClosing(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'total_outgoing_amount' => $amountOut,
                        'nr_of_outgoing_records' => $nrOut,
                        'nr_of_incoming_records' => $nrIn,
                        'total_incoming_amount' => $amountIn,
                    ]
                );
            });
        }

        $this->cache['OLD_PAYMENT_CLOSING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_PAYMENT_CLOSING');
        }

        return $_success;
    }

    protected function parseMANDATE_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_FILE'][$_position])) {
            $_success = $this->cache['MANDATE_FILE'][$_position]['success'];
            $this->position = $this->cache['MANDATE_FILE'][$_position]['position'];
            $this->value = $this->cache['MANDATE_FILE'][$_position]['value'];

            return $_success;
        }

        $_value86 = array();

        $_position81 = $this->position;
        $_cut82 = $this->cut;

        $this->cut = false;
        $_success = $this->parseOLD_MANDATE_OPENING_REC();

        if (!$_success && !$this->cut) {
            $this->position = $_position81;

            $_success = $this->parseMANDATE_OPENING_REC();
        }

        $this->cut = $_cut82;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value86[] = $this->value;

            $_value84 = array();
            $_cut85 = $this->cut;

            while (true) {
                $_position83 = $this->position;

                $this->cut = false;
                $_success = $this->parseMANDATE_REC();

                if (!$_success) {
                    break;
                }

                $_value84[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position83;
                $this->value = $_value84;
            }

            $this->cut = $_cut85;

            if ($_success) {
                $mands = $this->value;
            }
        }

        if ($_success) {
            $_value86[] = $this->value;

            $_success = $this->parseMANDATE_CLOSING_REC();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value86[] = $this->value;

            $this->value = $_value86;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$mands, &$close) {
                $mands[] = $close;
                return new FileNode(Layouts::LAYOUT_MANDATE_RESPONSE, $open, ...$mands);
            });
        }

        $this->cache['MANDATE_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_FILE');
        }

        return $_success;
    }

    protected function parseMANDATE_OPENING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_OPENING_REC'][$_position])) {
            $_success = $this->cache['MANDATE_OPENING_REC'][$_position]['success'];
            $this->position = $this->cache['MANDATE_OPENING_REC'][$_position]['position'];
            $this->value = $this->cache['MANDATE_OPENING_REC'][$_position]['value'];

            return $_success;
        }

        $_value87 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value87[] = $this->value;

            if (substr($this->string, $this->position, strlen('AUTOGIRO')) === 'AUTOGIRO') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO'));
                $this->position += strlen('AUTOGIRO');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO\'');
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value87[] = $this->value;

            if (substr($this->string, $this->position, strlen('  AG-MEDAVI')) === '  AG-MEDAVI') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('  AG-MEDAVI'));
                $this->position += strlen('  AG-MEDAVI');
            } else {
                $_success = false;

                $this->report($this->position, '\'  AG-MEDAVI\'');
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value87[] = $this->value;

            $this->value = $_value87;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Response\ResponseOpening(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'payee_bgc_number' => $bgcNr,
                        'payee_bankgiro' => $bg,
                    ]
                );
            });
        }

        $this->cache['MANDATE_OPENING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_OPENING_REC');
        }

        return $_success;
    }

    protected function parseOLD_MANDATE_OPENING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_MANDATE_OPENING_REC'][$_position])) {
            $_success = $this->cache['OLD_MANDATE_OPENING_REC'][$_position]['success'];
            $this->position = $this->cache['OLD_MANDATE_OPENING_REC'][$_position]['position'];
            $this->value = $this->cache['OLD_MANDATE_OPENING_REC'][$_position]['value'];

            return $_success;
        }

        $_value88 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value88[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900')) === '9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900'));
                $this->position += strlen('9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900\'');
            }
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value88[] = $this->value;

            if (substr($this->string, $this->position, strlen('AG-MEDAVI')) === 'AG-MEDAVI') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AG-MEDAVI'));
                $this->position += strlen('AG-MEDAVI');
            } else {
                $_success = false;

                $this->report($this->position, '\'AG-MEDAVI\'');
            }
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value88[] = $this->value;

            $this->value = $_value88;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bg) {
                return new Response\ResponseOpening(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'payee_bgc_number' => new BgcNumberNode($this->lineNr, ''),
                        'payee_bankgiro' => $bg,
                    ]
                );
            });
        }

        $this->cache['OLD_MANDATE_OPENING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_MANDATE_OPENING_REC');
        }

        return $_success;
    }

    protected function parseMANDATE_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_REC'][$_position])) {
            $_success = $this->cache['MANDATE_REC'][$_position]['success'];
            $this->position = $this->cache['MANDATE_REC'][$_position]['position'];
            $this->value = $this->cache['MANDATE_REC'][$_position]['value'];

            return $_success;
        }

        $_value95 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseACCOUNT16();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_position89 = $this->position;
            $_cut90 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS5();

            if (!$_success && !$this->cut) {
                $this->position = $_position89;

                if (substr($this->string, $this->position, strlen('00000')) === '00000') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen('00000'));
                    $this->position += strlen('00000');
                } else {
                    $_success = false;

                    $this->report($this->position, '\'00000\'');
                }
            }

            $this->cut = $_cut90;
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_position94 = $this->position;

            $_position92 = $this->position;
            $_cut93 = $this->cut;

            $this->cut = false;
            $_value91 = array();

            $_success = $this->parseA5();

            if ($_success) {
                $_value91[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value91[] = $this->value;

                $this->value = $_value91;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position92;
                $this->value = null;
            }

            $this->cut = $_cut93;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position94, $this->position - $_position94));
            }

            if ($_success) {
                $validDate = $this->value;
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value95[] = $this->value;

            $this->value = $_value95;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$account, &$id, &$info, &$status, &$date, &$validDate) {
                // If account is empty a valid bankgiro number may be read from the payer number field
                if (!trim($account->getValue())) {
                    $account = new ReferredAccountNode($account->getLineNr(), $payerNr->getValue());
                }

                $info->setAttribute('message_id', "73.info.{$info->getValue()}");
                $status->setAttribute('message_id', "73.status.{$status->getValue()}");

                $nodes = [
                    'payee_bankgiro' => $bg,
                    'payer_number' => $payerNr,
                    'account' => $account,
                    'id' => $id,
                    'info' => $info,
                    'status' => $status,
                    'date' => $date,
                ];

                // A mandate-valid-from-date is only present in the old layout
                if ($validDate) {
                    $nodes['valid_from_date'] = new TextNode($this->lineNr, (string)$validDate);
                }

                return new Response\MandateResponse($this->lineNr, $nodes);
            });
        }

        $this->cache['MANDATE_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_REC');
        }

        return $_success;
    }

    protected function parseMANDATE_CLOSING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_CLOSING_REC'][$_position])) {
            $_success = $this->cache['MANDATE_CLOSING_REC'][$_position]['success'];
            $this->position = $this->cache['MANDATE_CLOSING_REC'][$_position]['position'];
            $this->value = $this->cache['MANDATE_CLOSING_REC'][$_position]['value'];

            return $_success;
        }

        $_value96 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value96[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value96[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900')) === '9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900'));
                $this->position += strlen('9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900\'');
            }
        }

        if ($_success) {
            $_value96[] = $this->value;

            $_success = $this->parseINT7();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value96[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value96[] = $this->value;

            $this->value = $_value96;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrRecs) {
                return new Response\MandateResponseClosing($this->lineNr, ['date' => $date, 'nr_of_records' => $nrRecs]);
            });
        }

        $this->cache['MANDATE_CLOSING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_CLOSING_REC');
        }

        return $_success;
    }

    protected function parsePAYMENT_REJECTION_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REJECTION_FILE'][$_position])) {
            $_success = $this->cache['PAYMENT_REJECTION_FILE'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REJECTION_FILE'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REJECTION_FILE'][$_position]['value'];

            return $_success;
        }

        $_value102 = array();

        $_position97 = $this->position;
        $_cut98 = $this->cut;

        $this->cut = false;
        $_success = $this->parsePAYMENT_REJECTION_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position97;

            $_success = $this->parseOLD_PAYMENT_REJECTION_OPENING();
        }

        $this->cut = $_cut98;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value102[] = $this->value;

            $_value100 = array();
            $_cut101 = $this->cut;

            while (true) {
                $_position99 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_REJECTION_RECORD();

                if (!$_success) {
                    break;
                }

                $_value100[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position99;
                $this->value = $_value100;
            }

            $this->cut = $_cut101;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value102[] = $this->value;

            $_success = $this->parsePAYMENT_REJECTION_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value102[] = $this->value;

            $this->value = $_value102;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$recs, &$close) {
                $recs[] = $close;
                return new FileNode(Layouts::LAYOUT_PAYMENT_REJECTION, $open, ...$recs);
            });
        }

        $this->cache['PAYMENT_REJECTION_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REJECTION_FILE');
        }

        return $_success;
    }

    protected function parsePAYMENT_REJECTION_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REJECTION_OPENING'][$_position])) {
            $_success = $this->cache['PAYMENT_REJECTION_OPENING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REJECTION_OPENING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REJECTION_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value103 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value103[] = $this->value;

            if (substr($this->string, $this->position, strlen('AUTOGIRO')) === 'AUTOGIRO') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO'));
                $this->position += strlen('AUTOGIRO');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO\'');
            }
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value103[] = $this->value;

            if (substr($this->string, $this->position, strlen('  AVVISADE BET UPPDR  ')) === '  AVVISADE BET UPPDR  ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('  AVVISADE BET UPPDR  '));
                $this->position += strlen('  AVVISADE BET UPPDR  ');
            } else {
                $_success = false;

                $this->report($this->position, '\'  AVVISADE BET UPPDR  \'');
            }
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value103[] = $this->value;

            $this->value = $_value103;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Response\ResponseOpening(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'payee_bgc_number' => $bgcNr,
                        'payee_bankgiro' => $bg,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_REJECTION_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REJECTION_OPENING');
        }

        return $_success;
    }

    protected function parseOLD_PAYMENT_REJECTION_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_PAYMENT_REJECTION_OPENING'][$_position])) {
            $_success = $this->cache['OLD_PAYMENT_REJECTION_OPENING'][$_position]['success'];
            $this->position = $this->cache['OLD_PAYMENT_REJECTION_OPENING'][$_position]['position'];
            $this->value = $this->cache['OLD_PAYMENT_REJECTION_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value104 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value104[] = $this->value;

            if (substr($this->string, $this->position, strlen('AUTOGIRO')) === 'AUTOGIRO') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO'));
                $this->position += strlen('AUTOGIRO');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO\'');
            }
        }

        if ($_success) {
            $_value104[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900')) === '9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900'));
                $this->position += strlen('9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900\'');
            }
        }

        if ($_success) {
            $_value104[] = $this->value;

            if (substr($this->string, $this->position, strlen('FELLISTA REG.KONTRL')) === 'FELLISTA REG.KONTRL') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('FELLISTA REG.KONTRL'));
                $this->position += strlen('FELLISTA REG.KONTRL');
            } else {
                $_success = false;

                $this->report($this->position, '\'FELLISTA REG.KONTRL\'');
            }
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value104[] = $this->value;

            $this->value = $_value104;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Response\ResponseOpening(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'payee_bgc_number' => $bgcNr,
                        'payee_bankgiro' => $bg,
                    ]
                );
            });
        }

        $this->cache['OLD_PAYMENT_REJECTION_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_PAYMENT_REJECTION_OPENING');
        }

        return $_success;
    }

    protected function parsePAYMENT_REJECTION_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REJECTION_RECORD'][$_position])) {
            $_success = $this->cache['PAYMENT_REJECTION_RECORD'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REJECTION_RECORD'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REJECTION_RECORD'][$_position]['value'];

            return $_success;
        }

        $_value107 = array();

        $_position105 = $this->position;
        $_cut106 = $this->cut;

        $this->cut = false;
        $_success = $this->parsePAYMENT_REJECTION_INCOMING();

        if (!$_success && !$this->cut) {
            $this->position = $_position105;

            $_success = $this->parsePAYMENT_REJECTION_OUTGOING();
        }

        $this->cut = $_cut106;

        if ($_success) {
            $type = $this->value;
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value107[] = $this->value;

            $this->value = $_value107;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$type, &$date, &$ival, &$reps, &$payerNr, &$amount, &$ref, &$comment) {
                return new $type(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'interval' => $ival,
                        'repetitions' => $reps,
                        'payer_number' => $payerNr,
                        'amount' => $amount,
                        'reference' => $ref,
                        'comment' => $comment,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_REJECTION_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REJECTION_RECORD');
        }

        return $_success;
    }

    protected function parsePAYMENT_REJECTION_INCOMING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REJECTION_INCOMING'][$_position])) {
            $_success = $this->cache['PAYMENT_REJECTION_INCOMING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REJECTION_INCOMING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REJECTION_INCOMING'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen('82')) === '82') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('82'));
            $this->position += strlen('82');
        } else {
            $_success = false;

            $this->report($this->position, '\'82\'');
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return Response\IncomingPaymentRejectionResponse::CLASS;
            });
        }

        $this->cache['PAYMENT_REJECTION_INCOMING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REJECTION_INCOMING');
        }

        return $_success;
    }

    protected function parsePAYMENT_REJECTION_OUTGOING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REJECTION_OUTGOING'][$_position])) {
            $_success = $this->cache['PAYMENT_REJECTION_OUTGOING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REJECTION_OUTGOING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REJECTION_OUTGOING'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen('32')) === '32') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('32'));
            $this->position += strlen('32');
        } else {
            $_success = false;

            $this->report($this->position, '\'32\'');
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return Response\OutgoingPaymentRejectionResponse::CLASS;
            });
        }

        $this->cache['PAYMENT_REJECTION_OUTGOING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REJECTION_OUTGOING');
        }

        return $_success;
    }

    protected function parsePAYMENT_REJECTION_CLOSING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REJECTION_CLOSING'][$_position])) {
            $_success = $this->cache['PAYMENT_REJECTION_CLOSING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REJECTION_CLOSING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REJECTION_CLOSING'][$_position]['value'];

            return $_success;
        }

        $_value108 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value108[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value108[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900')) === '9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900'));
                $this->position += strlen('9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900\'');
            }
        }

        if ($_success) {
            $_value108[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value108[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value108[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value108[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value108[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value108[] = $this->value;

            $this->value = $_value108;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrOut, &$amountOut, &$nrIn, &$amountIn) {
                return new Response\PaymentRejectionResponseClosing(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'nr_of_outgoing_records' => $nrOut,
                        'total_outgoing_amount' => $amountOut,
                        'nr_of_incoming_records' => $nrIn,
                        'total_incoming_amount' => $amountIn,
                    ]
                );
            });
        }

        $this->cache['PAYMENT_REJECTION_CLOSING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REJECTION_CLOSING');
        }

        return $_success;
    }

    protected function parseOPENING_INTRO()
    {
        $_position = $this->position;

        if (isset($this->cache['OPENING_INTRO'][$_position])) {
            $_success = $this->cache['OPENING_INTRO'][$_position]['success'];
            $this->position = $this->cache['OPENING_INTRO'][$_position]['position'];
            $this->value = $this->cache['OPENING_INTRO'][$_position]['value'];

            return $_success;
        }

        $_value109 = array();

        if (substr($this->string, $this->position, strlen('01AUTOGIRO              ')) === '01AUTOGIRO              ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01AUTOGIRO              '));
            $this->position += strlen('01AUTOGIRO              ');
        } else {
            $_success = false;

            $this->report($this->position, '\'01AUTOGIRO              \'');
        }

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value109[] = $this->value;

            if (substr($this->string, $this->position, strlen('            ')) === '            ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('            '));
                $this->position += strlen('            ');
            } else {
                $_success = false;

                $this->report($this->position, '\'            \'');
            }
        }

        if ($_success) {
            $_value109[] = $this->value;

            $this->value = $_value109;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date) {
                return $date;
            });
        }

        $this->cache['OPENING_INTRO'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OPENING_INTRO');
        }

        return $_success;
    }

    protected function parseOLD_OPENING_INTRO()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_OPENING_INTRO'][$_position])) {
            $_success = $this->cache['OLD_OPENING_INTRO'][$_position]['success'];
            $this->position = $this->cache['OLD_OPENING_INTRO'][$_position]['position'];
            $this->value = $this->cache['OLD_OPENING_INTRO'][$_position]['value'];

            return $_success;
        }

        $_value110 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value110[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value110[] = $this->value;

            if (substr($this->string, $this->position, strlen('AUTOGIRO9900')) === 'AUTOGIRO9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO9900'));
                $this->position += strlen('AUTOGIRO9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO9900\'');
            }
        }

        if ($_success) {
            $_value110[] = $this->value;

            $this->value = $_value110;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date) {
                return $date;
            });
        }

        $this->cache['OLD_OPENING_INTRO'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_OPENING_INTRO');
        }

        return $_success;
    }

    protected function parseAMENDMENT_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['AMENDMENT_FILE'][$_position])) {
            $_success = $this->cache['AMENDMENT_FILE'][$_position]['success'];
            $this->position = $this->cache['AMENDMENT_FILE'][$_position]['position'];
            $this->value = $this->cache['AMENDMENT_FILE'][$_position]['value'];

            return $_success;
        }

        $_value116 = array();

        $_position111 = $this->position;
        $_cut112 = $this->cut;

        $this->cut = false;
        $_success = $this->parseOLD_AMENDMENT_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position111;

            $_success = $this->parseAMENDMENT_OPENING();
        }

        $this->cut = $_cut112;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value116[] = $this->value;

            $_value114 = array();
            $_cut115 = $this->cut;

            while (true) {
                $_position113 = $this->position;

                $this->cut = false;
                $_success = $this->parseAMENDMENT_RECORD();

                if (!$_success) {
                    break;
                }

                $_value114[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position113;
                $this->value = $_value114;
            }

            $this->cut = $_cut115;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value116[] = $this->value;

            $_success = $this->parseAMENDMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value116[] = $this->value;

            $this->value = $_value116;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$recs, &$close) {
                $recs[] = $close;
                return new FileNode(Layouts::LAYOUT_AMENDMENT_RESPONSE, $open, ...$recs);
            });
        }

        $this->cache['AMENDMENT_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AMENDMENT_FILE');
        }

        return $_success;
    }

    protected function parseAMENDMENT_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['AMENDMENT_OPENING'][$_position])) {
            $_success = $this->cache['AMENDMENT_OPENING'][$_position]['success'];
            $this->position = $this->cache['AMENDMENT_OPENING'][$_position]['position'];
            $this->value = $this->cache['AMENDMENT_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value117 = array();

        $_success = $this->parseOPENING_INTRO();

        if ($_success) {
            $date = $this->value;
        }

        if ($_success) {
            $_value117[] = $this->value;

            if (substr($this->string, $this->position, strlen('MAKULERING/ÄNDRING  ')) === 'MAKULERING/ÄNDRING  ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('MAKULERING/ÄNDRING  '));
                $this->position += strlen('MAKULERING/ÄNDRING  ');
            } else {
                $_success = false;

                $this->report($this->position, '\'MAKULERING/ÄNDRING  \'');
            }
        }

        if ($_success) {
            $_value117[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value117[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value117[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value117[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value117[] = $this->value;

            $this->value = $_value117;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Response\ResponseOpening(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'payee_bgc_number' => $bgcNr,
                        'payee_bankgiro' => $bg,
                    ]
                );
            });
        }

        $this->cache['AMENDMENT_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AMENDMENT_OPENING');
        }

        return $_success;
    }

    protected function parseOLD_AMENDMENT_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_AMENDMENT_OPENING'][$_position])) {
            $_success = $this->cache['OLD_AMENDMENT_OPENING'][$_position]['success'];
            $this->position = $this->cache['OLD_AMENDMENT_OPENING'][$_position]['position'];
            $this->value = $this->cache['OLD_AMENDMENT_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value118 = array();

        $_success = $this->parseOLD_OPENING_INTRO();

        if ($_success) {
            $date = $this->value;
        }

        if ($_success) {
            $_value118[] = $this->value;

            if (substr($this->string, $this->position, strlen('MAK/ÄNDRINGSLISTA   ')) === 'MAK/ÄNDRINGSLISTA   ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('MAK/ÄNDRINGSLISTA   '));
                $this->position += strlen('MAK/ÄNDRINGSLISTA   ');
            } else {
                $_success = false;

                $this->report($this->position, '\'MAK/ÄNDRINGSLISTA   \'');
            }
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value118[] = $this->value;

            $this->value = $_value118;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Response\ResponseOpening(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'payee_bgc_number' => $bgcNr,
                        'payee_bankgiro' => $bg,
                    ]
                );
            });
        }

        $this->cache['OLD_AMENDMENT_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_AMENDMENT_OPENING');
        }

        return $_success;
    }

    protected function parseAMENDMENT_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['AMENDMENT_RECORD'][$_position])) {
            $_success = $this->cache['AMENDMENT_RECORD'][$_position]['success'];
            $this->position = $this->cache['AMENDMENT_RECORD'][$_position]['position'];
            $this->value = $this->cache['AMENDMENT_RECORD'][$_position]['value'];

            return $_success;
        }

        $_value121 = array();

        $_position119 = $this->position;
        $_cut120 = $this->cut;

        $this->cut = false;
        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if (!$_success) {
            $_success = true;
            $this->value = null;
        } else {
            $_success = false;
        }

        $this->position = $_position119;
        $this->cut = $_cut120;

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $tc = $this->value;
            }
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $type = $this->value;
            }
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseA8();
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseA8();
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value121[] = $this->value;

            $this->value = $_value121;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$tc, &$date, &$payerNr, &$type, &$amount, &$ref, &$comment) {
                $tc->setAttribute('message_id', Layouts::LAYOUT_AMENDMENT_RESPONSE . '.TC.' . $tc->getValue());
                return new Response\AmendmentResponse(
                    $this->lineNr,
                    [
                        'code' => $tc,
                        'date' => $date,
                        'payer_number' => $payerNr,
                        'type' => $type,
                        'amount' => $amount,
                        'reference' => $ref,
                        'comment' => $comment,
                    ]
                );
            });
        }

        $this->cache['AMENDMENT_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AMENDMENT_RECORD');
        }

        return $_success;
    }

    protected function parseAMENDMENT_CLOSING()
    {
        $_position = $this->position;

        if (isset($this->cache['AMENDMENT_CLOSING'][$_position])) {
            $_success = $this->cache['AMENDMENT_CLOSING'][$_position]['success'];
            $this->position = $this->cache['AMENDMENT_CLOSING'][$_position]['position'];
            $this->value = $this->cache['AMENDMENT_CLOSING'][$_position]['value'];

            return $_success;
        }

        $_value122 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value122[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900')) === '9900') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900'));
                $this->position += strlen('9900');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900\'');
            }
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseA4();
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value122[] = $this->value;

            $this->value = $_value122;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$amountOut, &$nrOut, &$nrIn, &$amountIn) {
                return new Response\ResponseClosing(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'nr_of_amended_outgoing_records' => $nrOut,
                        'total_amended_outgoing_amount' => $amountOut,
                        'nr_of_amended_incoming_records' => $nrIn,
                        'total_amended_incoming_amount' => $amountIn,
                    ]
                );
            });
        }

        $this->cache['AMENDMENT_CLOSING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AMENDMENT_CLOSING');
        }

        return $_success;
    }

    protected function parseACCOUNT16()
    {
        $_position = $this->position;

        if (isset($this->cache['ACCOUNT16'][$_position])) {
            $_success = $this->cache['ACCOUNT16'][$_position]['success'];
            $this->position = $this->cache['ACCOUNT16'][$_position]['position'];
            $this->value = $this->cache['ACCOUNT16'][$_position]['value'];

            return $_success;
        }

        $_position124 = $this->position;

        $_value123 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value123[] = $this->value;

            $this->value = $_value123;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position124, $this->position - $_position124));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new AccountNode($this->lineNr + 1, $number);
            });
        }

        $this->cache['ACCOUNT16'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'ACCOUNT16');
        }

        return $_success;
    }

    protected function parseACCOUNT35()
    {
        $_position = $this->position;

        if (isset($this->cache['ACCOUNT35'][$_position])) {
            $_success = $this->cache['ACCOUNT35'][$_position]['success'];
            $this->position = $this->cache['ACCOUNT35'][$_position]['position'];
            $this->value = $this->cache['ACCOUNT35'][$_position]['value'];

            return $_success;
        }

        $_position126 = $this->position;

        $_value125 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value125[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value125[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value125[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value125[] = $this->value;

            $this->value = $_value125;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position126, $this->position - $_position126));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new AccountNode($this->lineNr + 1, $number);
            });
        }

        $this->cache['ACCOUNT35'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'ACCOUNT35');
        }

        return $_success;
    }

    protected function parseAMOUNT12()
    {
        $_position = $this->position;

        if (isset($this->cache['AMOUNT12'][$_position])) {
            $_success = $this->cache['AMOUNT12'][$_position]['success'];
            $this->position = $this->cache['AMOUNT12'][$_position]['position'];
            $this->value = $this->cache['AMOUNT12'][$_position]['value'];

            return $_success;
        }

        $_position128 = $this->position;

        $_value127 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value127[] = $this->value;

            $this->value = $_value127;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position128, $this->position - $_position128));
        }

        if ($_success) {
            $amount = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$amount) {
                return new AmountNode($this->lineNr + 1, $amount);
            });
        }

        $this->cache['AMOUNT12'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AMOUNT12');
        }

        return $_success;
    }

    protected function parseAMOUNT18()
    {
        $_position = $this->position;

        if (isset($this->cache['AMOUNT18'][$_position])) {
            $_success = $this->cache['AMOUNT18'][$_position]['success'];
            $this->position = $this->cache['AMOUNT18'][$_position]['position'];
            $this->value = $this->cache['AMOUNT18'][$_position]['value'];

            return $_success;
        }

        $_position130 = $this->position;

        $_value129 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value129[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value129[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value129[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value129[] = $this->value;

            $this->value = $_value129;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position130, $this->position - $_position130));
        }

        if ($_success) {
            $amount = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$amount) {
                return new AmountNode($this->lineNr + 1, $amount);
            });
        }

        $this->cache['AMOUNT18'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AMOUNT18');
        }

        return $_success;
    }

    protected function parseBANKGIRO()
    {
        $_position = $this->position;

        if (isset($this->cache['BANKGIRO'][$_position])) {
            $_success = $this->cache['BANKGIRO'][$_position]['success'];
            $this->position = $this->cache['BANKGIRO'][$_position]['position'];
            $this->value = $this->cache['BANKGIRO'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseA10();

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new BankgiroNode($this->lineNr + 1, $number);
            });
        }

        $this->cache['BANKGIRO'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'BANKGIRO');
        }

        return $_success;
    }

    protected function parseID()
    {
        $_position = $this->position;

        if (isset($this->cache['ID'][$_position])) {
            $_success = $this->cache['ID'][$_position]['success'];
            $this->position = $this->cache['ID'][$_position]['position'];
            $this->value = $this->cache['ID'][$_position]['value'];

            return $_success;
        }

        $_position132 = $this->position;

        $_value131 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value131[] = $this->value;

            $this->value = $_value131;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position132, $this->position - $_position132));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new IdNode($this->lineNr + 1, $number);
            });
        }

        $this->cache['ID'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'ID');
        }

        return $_success;
    }

    protected function parseBGC_NR()
    {
        $_position = $this->position;

        if (isset($this->cache['BGC_NR'][$_position])) {
            $_success = $this->cache['BGC_NR'][$_position]['success'];
            $this->position = $this->cache['BGC_NR'][$_position]['position'];
            $this->value = $this->cache['BGC_NR'][$_position]['value'];

            return $_success;
        }

        $_position134 = $this->position;

        $_value133 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value133[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value133[] = $this->value;

            $this->value = $_value133;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position134, $this->position - $_position134));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new BgcNumberNode($this->lineNr + 1, $nr);
            });
        }

        $this->cache['BGC_NR'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'BGC_NR');
        }

        return $_success;
    }

    protected function parseDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['DATE'][$_position])) {
            $_success = $this->cache['DATE'][$_position]['success'];
            $this->position = $this->cache['DATE'][$_position]['position'];
            $this->value = $this->cache['DATE'][$_position]['value'];

            return $_success;
        }

        $_position136 = $this->position;

        $_value135 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value135[] = $this->value;

            $this->value = $_value135;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position136, $this->position - $_position136));
        }

        if ($_success) {
            $date = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date) {
                return new DateNode($this->lineNr + 1, $date);
            });
        }

        $this->cache['DATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'DATE');
        }

        return $_success;
    }

    protected function parseIMMEDIATE_DATE()
    {
        $_position = $this->position;

        if (isset($this->cache['IMMEDIATE_DATE'][$_position])) {
            $_success = $this->cache['IMMEDIATE_DATE'][$_position]['success'];
            $this->position = $this->cache['IMMEDIATE_DATE'][$_position]['position'];
            $this->value = $this->cache['IMMEDIATE_DATE'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen('GENAST  ')) === 'GENAST  ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('GENAST  '));
            $this->position += strlen('GENAST  ');
        } else {
            $_success = false;

            $this->report($this->position, '\'GENAST  \'');
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return new ImmediateDateNode($this->lineNr + 1);
            });
        }

        $this->cache['IMMEDIATE_DATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'IMMEDIATE_DATE');
        }

        return $_success;
    }

    protected function parseDATETIME()
    {
        $_position = $this->position;

        if (isset($this->cache['DATETIME'][$_position])) {
            $_success = $this->cache['DATETIME'][$_position]['success'];
            $this->position = $this->cache['DATETIME'][$_position]['position'];
            $this->value = $this->cache['DATETIME'][$_position]['value'];

            return $_success;
        }

        $_position138 = $this->position;

        $_value137 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value137[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value137[] = $this->value;

            $this->value = $_value137;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position138, $this->position - $_position138));
        }

        if ($_success) {
            $datetime = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$datetime) {
                return new DateTimeNode($this->lineNr + 1, $datetime);
            });
        }

        $this->cache['DATETIME'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'DATETIME');
        }

        return $_success;
    }

    protected function parseINTERVAL()
    {
        $_position = $this->position;

        if (isset($this->cache['INTERVAL'][$_position])) {
            $_success = $this->cache['INTERVAL'][$_position]['success'];
            $this->position = $this->cache['INTERVAL'][$_position]['position'];
            $this->value = $this->cache['INTERVAL'][$_position]['value'];

            return $_success;
        }

        $_position139 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position139, $this->position - $_position139));
        }

        if ($_success) {
            $interval = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$interval) {
                return new IntervalNode($this->lineNr + 1, $interval);
            });
        }

        $this->cache['INTERVAL'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'INTERVAL');
        }

        return $_success;
    }

    protected function parseMSG1()
    {
        $_position = $this->position;

        if (isset($this->cache['MSG1'][$_position])) {
            $_success = $this->cache['MSG1'][$_position]['success'];
            $this->position = $this->cache['MSG1'][$_position]['position'];
            $this->value = $this->cache['MSG1'][$_position]['value'];

            return $_success;
        }

        $_position140 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position140, $this->position - $_position140));
        }

        if ($_success) {
            $msg = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$msg) {
                return new MessageNode($this->lineNr + 1, $msg);
            });
        }

        $this->cache['MSG1'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MSG1');
        }

        return $_success;
    }

    protected function parseMSG2()
    {
        $_position = $this->position;

        if (isset($this->cache['MSG2'][$_position])) {
            $_success = $this->cache['MSG2'][$_position]['success'];
            $this->position = $this->cache['MSG2'][$_position]['position'];
            $this->value = $this->cache['MSG2'][$_position]['value'];

            return $_success;
        }

        $_position142 = $this->position;

        $_value141 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value141[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value141[] = $this->value;

            $this->value = $_value141;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position142, $this->position - $_position142));
        }

        if ($_success) {
            $msg = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$msg) {
                return new MessageNode($this->lineNr + 1, $msg);
            });
        }

        $this->cache['MSG2'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MSG2');
        }

        return $_success;
    }

    protected function parsePAYER_NR()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYER_NR'][$_position])) {
            $_success = $this->cache['PAYER_NR'][$_position]['success'];
            $this->position = $this->cache['PAYER_NR'][$_position]['position'];
            $this->value = $this->cache['PAYER_NR'][$_position]['value'];

            return $_success;
        }

        $_position144 = $this->position;

        $_value143 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value143[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value143[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value143[] = $this->value;

            $this->value = $_value143;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position144, $this->position - $_position144));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new PayerNumberNode($this->lineNr + 1, $nr);
            });
        }

        $this->cache['PAYER_NR'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYER_NR');
        }

        return $_success;
    }

    protected function parseREPS()
    {
        $_position = $this->position;

        if (isset($this->cache['REPS'][$_position])) {
            $_success = $this->cache['REPS'][$_position]['success'];
            $this->position = $this->cache['REPS'][$_position]['position'];
            $this->value = $this->cache['REPS'][$_position]['value'];

            return $_success;
        }

        $_position146 = $this->position;

        $_value145 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value145[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value145[] = $this->value;

            $this->value = $_value145;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position146, $this->position - $_position146));
        }

        if ($_success) {
            $repetitions = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$repetitions) {
                return new RepetitionsNode($this->lineNr + 1, $repetitions);
            });
        }

        $this->cache['REPS'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REPS');
        }

        return $_success;
    }

    protected function parseINT5()
    {
        $_position = $this->position;

        if (isset($this->cache['INT5'][$_position])) {
            $_success = $this->cache['INT5'][$_position]['success'];
            $this->position = $this->cache['INT5'][$_position]['position'];
            $this->value = $this->cache['INT5'][$_position]['value'];

            return $_success;
        }

        $_position147 = $this->position;

        $_success = $this->parseA5();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position147, $this->position - $_position147));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new TextNode($this->lineNr + 1, $integer, '/^\d{5}$/');
            });
        }

        $this->cache['INT5'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'INT5');
        }

        return $_success;
    }

    protected function parseINT6()
    {
        $_position = $this->position;

        if (isset($this->cache['INT6'][$_position])) {
            $_success = $this->cache['INT6'][$_position]['success'];
            $this->position = $this->cache['INT6'][$_position]['position'];
            $this->value = $this->cache['INT6'][$_position]['value'];

            return $_success;
        }

        $_position149 = $this->position;

        $_value148 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value148[] = $this->value;

            $this->value = $_value148;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position149, $this->position - $_position149));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new TextNode($this->lineNr + 1, $integer, '/^\d{6}$/');
            });
        }

        $this->cache['INT6'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'INT6');
        }

        return $_success;
    }

    protected function parseINT7()
    {
        $_position = $this->position;

        if (isset($this->cache['INT7'][$_position])) {
            $_success = $this->cache['INT7'][$_position]['success'];
            $this->position = $this->cache['INT7'][$_position]['position'];
            $this->value = $this->cache['INT7'][$_position]['value'];

            return $_success;
        }

        $_position151 = $this->position;

        $_value150 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value150[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value150[] = $this->value;

            $this->value = $_value150;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position151, $this->position - $_position151));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new TextNode($this->lineNr + 1, $integer, '/^\d{7}$/');
            });
        }

        $this->cache['INT7'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'INT7');
        }

        return $_success;
    }

    protected function parseINT8()
    {
        $_position = $this->position;

        if (isset($this->cache['INT8'][$_position])) {
            $_success = $this->cache['INT8'][$_position]['success'];
            $this->position = $this->cache['INT8'][$_position]['position'];
            $this->value = $this->cache['INT8'][$_position]['value'];

            return $_success;
        }

        $_position153 = $this->position;

        $_value152 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value152[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value152[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value152[] = $this->value;

            $this->value = $_value152;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position153, $this->position - $_position153));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new TextNode($this->lineNr + 1, $integer, '/^\d{8}$/');
            });
        }

        $this->cache['INT8'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'INT8');
        }

        return $_success;
    }

    protected function parseINT12()
    {
        $_position = $this->position;

        if (isset($this->cache['INT12'][$_position])) {
            $_success = $this->cache['INT12'][$_position]['success'];
            $this->position = $this->cache['INT12'][$_position]['position'];
            $this->value = $this->cache['INT12'][$_position]['value'];

            return $_success;
        }

        $_position155 = $this->position;

        $_value154 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value154[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value154[] = $this->value;

            $this->value = $_value154;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position155, $this->position - $_position155));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new TextNode($this->lineNr + 1, $integer, '/^\d{12}$/');
            });
        }

        $this->cache['INT12'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'INT12');
        }

        return $_success;
    }

    protected function parseVAR_TXT()
    {
        $_position = $this->position;

        if (isset($this->cache['VAR_TXT'][$_position])) {
            $_success = $this->cache['VAR_TXT'][$_position]['success'];
            $this->position = $this->cache['VAR_TXT'][$_position]['position'];
            $this->value = $this->cache['VAR_TXT'][$_position]['value'];

            return $_success;
        }

        $_position159 = $this->position;

        $_value157 = array();
        $_cut158 = $this->cut;

        while (true) {
            $_position156 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success) {
                break;
            }

            $_value157[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position156;
            $this->value = $_value157;
        }

        $this->cut = $_cut158;

        if ($_success) {
            $this->value = strval(substr($this->string, $_position159, $this->position - $_position159));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text);
            });
        }

        $this->cache['VAR_TXT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'VAR_TXT');
        }

        return $_success;
    }

    protected function parseTXT16()
    {
        $_position = $this->position;

        if (isset($this->cache['TXT16'][$_position])) {
            $_success = $this->cache['TXT16'][$_position]['success'];
            $this->position = $this->cache['TXT16'][$_position]['position'];
            $this->value = $this->cache['TXT16'][$_position]['value'];

            return $_success;
        }

        $_position193 = $this->position;

        $_value192 = array();

        $_position160 = $this->position;
        $_cut161 = $this->cut;

        $this->cut = false;
        $_success = $this->parseA();

        if (!$_success && !$this->cut) {
            $_success = true;
            $this->position = $_position160;
            $this->value = null;
        }

        $this->cut = $_cut161;

        if ($_success) {
            $_value192[] = $this->value;

            $_position162 = $this->position;
            $_cut163 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position162;
                $this->value = null;
            }

            $this->cut = $_cut163;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position164 = $this->position;
            $_cut165 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position164;
                $this->value = null;
            }

            $this->cut = $_cut165;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position166 = $this->position;
            $_cut167 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position166;
                $this->value = null;
            }

            $this->cut = $_cut167;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position168 = $this->position;
            $_cut169 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position168;
                $this->value = null;
            }

            $this->cut = $_cut169;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position170 = $this->position;
            $_cut171 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position170;
                $this->value = null;
            }

            $this->cut = $_cut171;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position172 = $this->position;
            $_cut173 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position172;
                $this->value = null;
            }

            $this->cut = $_cut173;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position174 = $this->position;
            $_cut175 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position174;
                $this->value = null;
            }

            $this->cut = $_cut175;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position176 = $this->position;
            $_cut177 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position176;
                $this->value = null;
            }

            $this->cut = $_cut177;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position178 = $this->position;
            $_cut179 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position178;
                $this->value = null;
            }

            $this->cut = $_cut179;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position180 = $this->position;
            $_cut181 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position180;
                $this->value = null;
            }

            $this->cut = $_cut181;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position182 = $this->position;
            $_cut183 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position182;
                $this->value = null;
            }

            $this->cut = $_cut183;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position184 = $this->position;
            $_cut185 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position184;
                $this->value = null;
            }

            $this->cut = $_cut185;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position186 = $this->position;
            $_cut187 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position186;
                $this->value = null;
            }

            $this->cut = $_cut187;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position188 = $this->position;
            $_cut189 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position188;
                $this->value = null;
            }

            $this->cut = $_cut189;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $_position190 = $this->position;
            $_cut191 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position190;
                $this->value = null;
            }

            $this->cut = $_cut191;
        }

        if ($_success) {
            $_value192[] = $this->value;

            $this->value = $_value192;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position193, $this->position - $_position193));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text);
            });
        }

        $this->cache['TXT16'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'TXT16');
        }

        return $_success;
    }

    protected function parseTXT48()
    {
        $_position = $this->position;

        if (isset($this->cache['TXT48'][$_position])) {
            $_success = $this->cache['TXT48'][$_position]['success'];
            $this->position = $this->cache['TXT48'][$_position]['position'];
            $this->value = $this->cache['TXT48'][$_position]['value'];

            return $_success;
        }

        $_position195 = $this->position;

        $_value194 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value194[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value194[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value194[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value194[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value194[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value194[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value194[] = $this->value;

            $this->value = $_value194;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position195, $this->position - $_position195));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text);
            });
        }

        $this->cache['TXT48'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'TXT48');
        }

        return $_success;
    }

    protected function parseA()
    {
        $_position = $this->position;

        if (isset($this->cache['A'][$_position])) {
            $_success = $this->cache['A'][$_position]['success'];
            $this->position = $this->cache['A'][$_position]['position'];
            $this->value = $this->cache['A'][$_position]['value'];

            return $_success;
        }

        $_value198 = array();

        $_position196 = $this->position;
        $_cut197 = $this->cut;

        $this->cut = false;
        $_success = $this->parseEOL();

        if (!$_success) {
            $_success = true;
            $this->value = null;
        } else {
            $_success = false;
        }

        $this->position = $_position196;
        $this->cut = $_cut197;

        if ($_success) {
            $_value198[] = $this->value;

            if ($this->position < strlen($this->string)) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }
        }

        if ($_success) {
            $_value198[] = $this->value;

            $this->value = $_value198;
        }

        $this->cache['A'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, "ALPHA-NUMERIC");
        }

        return $_success;
    }

    protected function parseA2()
    {
        $_position = $this->position;

        if (isset($this->cache['A2'][$_position])) {
            $_success = $this->cache['A2'][$_position]['success'];
            $this->position = $this->cache['A2'][$_position]['position'];
            $this->value = $this->cache['A2'][$_position]['value'];

            return $_success;
        }

        $_position200 = $this->position;

        $_value199 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value199[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value199[] = $this->value;

            $this->value = $_value199;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position200, $this->position - $_position200));
        }

        $this->cache['A2'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'A2');
        }

        return $_success;
    }

    protected function parseA4()
    {
        $_position = $this->position;

        if (isset($this->cache['A4'][$_position])) {
            $_success = $this->cache['A4'][$_position]['success'];
            $this->position = $this->cache['A4'][$_position]['position'];
            $this->value = $this->cache['A4'][$_position]['value'];

            return $_success;
        }

        $_position202 = $this->position;

        $_value201 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value201[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value201[] = $this->value;

            $this->value = $_value201;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position202, $this->position - $_position202));
        }

        $this->cache['A4'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'A4');
        }

        return $_success;
    }

    protected function parseA5()
    {
        $_position = $this->position;

        if (isset($this->cache['A5'][$_position])) {
            $_success = $this->cache['A5'][$_position]['success'];
            $this->position = $this->cache['A5'][$_position]['position'];
            $this->value = $this->cache['A5'][$_position]['value'];

            return $_success;
        }

        $_position204 = $this->position;

        $_value203 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value203[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value203[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value203[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value203[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value203[] = $this->value;

            $this->value = $_value203;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position204, $this->position - $_position204));
        }

        $this->cache['A5'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'A5');
        }

        return $_success;
    }

    protected function parseA8()
    {
        $_position = $this->position;

        if (isset($this->cache['A8'][$_position])) {
            $_success = $this->cache['A8'][$_position]['success'];
            $this->position = $this->cache['A8'][$_position]['position'];
            $this->value = $this->cache['A8'][$_position]['value'];

            return $_success;
        }

        $_position206 = $this->position;

        $_value205 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value205[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value205[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value205[] = $this->value;

            $this->value = $_value205;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position206, $this->position - $_position206));
        }

        $this->cache['A8'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'A8');
        }

        return $_success;
    }

    protected function parseA10()
    {
        $_position = $this->position;

        if (isset($this->cache['A10'][$_position])) {
            $_success = $this->cache['A10'][$_position]['success'];
            $this->position = $this->cache['A10'][$_position]['position'];
            $this->value = $this->cache['A10'][$_position]['value'];

            return $_success;
        }

        $_position208 = $this->position;

        $_value207 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value207[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value207[] = $this->value;

            $this->value = $_value207;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position208, $this->position - $_position208));
        }

        $this->cache['A10'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'A10');
        }

        return $_success;
    }

    protected function parseS()
    {
        $_position = $this->position;

        if (isset($this->cache['S'][$_position])) {
            $_success = $this->cache['S'][$_position]['success'];
            $this->position = $this->cache['S'][$_position]['position'];
            $this->value = $this->cache['S'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen(' ')) === ' ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen(' '));
            $this->position += strlen(' ');
        } else {
            $_success = false;

            $this->report($this->position, '\' \'');
        }

        $this->cache['S'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, "SPACE");
        }

        return $_success;
    }

    protected function parseS4()
    {
        $_position = $this->position;

        if (isset($this->cache['S4'][$_position])) {
            $_success = $this->cache['S4'][$_position]['success'];
            $this->position = $this->cache['S4'][$_position]['position'];
            $this->value = $this->cache['S4'][$_position]['value'];

            return $_success;
        }

        $_position210 = $this->position;

        $_value209 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value209[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value209[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value209[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value209[] = $this->value;

            $this->value = $_value209;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position210, $this->position - $_position210));
        }

        $this->cache['S4'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'S4');
        }

        return $_success;
    }

    protected function parseS5()
    {
        $_position = $this->position;

        if (isset($this->cache['S5'][$_position])) {
            $_success = $this->cache['S5'][$_position]['success'];
            $this->position = $this->cache['S5'][$_position]['position'];
            $this->value = $this->cache['S5'][$_position]['value'];

            return $_success;
        }

        $_position212 = $this->position;

        $_value211 = array();

        $_success = $this->parseS4();

        if ($_success) {
            $_value211[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value211[] = $this->value;

            $this->value = $_value211;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position212, $this->position - $_position212));
        }

        $this->cache['S5'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'S5');
        }

        return $_success;
    }

    protected function parseS10()
    {
        $_position = $this->position;

        if (isset($this->cache['S10'][$_position])) {
            $_success = $this->cache['S10'][$_position]['success'];
            $this->position = $this->cache['S10'][$_position]['position'];
            $this->value = $this->cache['S10'][$_position]['value'];

            return $_success;
        }

        $_position214 = $this->position;

        $_value213 = array();

        $_success = $this->parseS5();

        if ($_success) {
            $_value213[] = $this->value;

            $_success = $this->parseS5();
        }

        if ($_success) {
            $_value213[] = $this->value;

            $this->value = $_value213;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position214, $this->position - $_position214));
        }

        $this->cache['S10'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'S10');
        }

        return $_success;
    }

    protected function parseS20()
    {
        $_position = $this->position;

        if (isset($this->cache['S20'][$_position])) {
            $_success = $this->cache['S20'][$_position]['success'];
            $this->position = $this->cache['S20'][$_position]['position'];
            $this->value = $this->cache['S20'][$_position]['value'];

            return $_success;
        }

        $_position216 = $this->position;

        $_value215 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value215[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value215[] = $this->value;

            $this->value = $_value215;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position216, $this->position - $_position216));
        }

        $this->cache['S20'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'S20');
        }

        return $_success;
    }

    protected function parseEOR()
    {
        $_position = $this->position;

        if (isset($this->cache['EOR'][$_position])) {
            $_success = $this->cache['EOR'][$_position]['success'];
            $this->position = $this->cache['EOR'][$_position]['position'];
            $this->value = $this->cache['EOR'][$_position]['value'];

            return $_success;
        }

        $_value222 = array();

        $_value218 = array();
        $_cut219 = $this->cut;

        while (true) {
            $_position217 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success) {
                break;
            }

            $_value218[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position217;
            $this->value = $_value218;
        }

        $this->cut = $_cut219;

        if ($_success) {
            $_value222[] = $this->value;

            $_position220 = $this->position;
            $_cut221 = $this->cut;

            $this->cut = false;
            $_success = $this->parseEOL();

            if (!$_success && !$this->cut) {
                $this->position = $_position220;

                $_success = $this->parseEOF();
            }

            $this->cut = $_cut221;
        }

        if ($_success) {
            $_value222[] = $this->value;

            $this->value = $_value222;
        }

        $this->cache['EOR'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, "END_OF_RECORD");
        }

        return $_success;
    }

    protected function parseEOL()
    {
        $_position = $this->position;

        if (isset($this->cache['EOL'][$_position])) {
            $_success = $this->cache['EOL'][$_position]['success'];
            $this->position = $this->cache['EOL'][$_position]['position'];
            $this->value = $this->cache['EOL'][$_position]['value'];

            return $_success;
        }

        $_value225 = array();

        $_position223 = $this->position;
        $_cut224 = $this->cut;

        $this->cut = false;
        if (substr($this->string, $this->position, strlen("\r")) === "\r") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("\r"));
            $this->position += strlen("\r");
        } else {
            $_success = false;

            $this->report($this->position, '"\\r"');
        }

        if (!$_success && !$this->cut) {
            $_success = true;
            $this->position = $_position223;
            $this->value = null;
        }

        $this->cut = $_cut224;

        if ($_success) {
            $_value225[] = $this->value;

            if (substr($this->string, $this->position, strlen("\n")) === "\n") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("\n"));
                $this->position += strlen("\n");
            } else {
                $_success = false;

                $this->report($this->position, '"\\n"');
            }
        }

        if ($_success) {
            $_value225[] = $this->value;

            $this->value = $_value225;
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                $this->lineNr++;
            });
        }

        $this->cache['EOL'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, "END_OF_LINE");
        }

        return $_success;
    }

    protected function parseEOF()
    {
        $_position = $this->position;

        if (isset($this->cache['EOF'][$_position])) {
            $_success = $this->cache['EOF'][$_position]['success'];
            $this->position = $this->cache['EOF'][$_position]['position'];
            $this->value = $this->cache['EOF'][$_position]['value'];

            return $_success;
        }

        $_position226 = $this->position;
        $_cut227 = $this->cut;

        $this->cut = false;
        if ($this->position < strlen($this->string)) {
            $_success = true;
            $this->value = substr($this->string, $this->position, 1);
            $this->position += 1;
        } else {
            $_success = false;
        }

        if (!$_success) {
            $_success = true;
            $this->value = null;
        } else {
            $_success = false;
        }

        $this->position = $_position226;
        $this->cut = $_cut227;

        $this->cache['EOF'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, "END_OF_FILE");
        }

        return $_success;
    }

    protected function parseVOID()
    {
        $_position = $this->position;

        if (isset($this->cache['VOID'][$_position])) {
            $_success = $this->cache['VOID'][$_position]['success'];
            $this->position = $this->cache['VOID'][$_position]['position'];
            $this->value = $this->cache['VOID'][$_position]['value'];

            return $_success;
        }

        $_value231 = array();
        $_cut232 = $this->cut;

        while (true) {
            $_position230 = $this->position;

            $this->cut = false;
            $_position228 = $this->position;
            $_cut229 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS();

            if (!$_success && !$this->cut) {
                $this->position = $_position228;

                if (substr($this->string, $this->position, strlen("\t")) === "\t") {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen("\t"));
                    $this->position += strlen("\t");
                } else {
                    $_success = false;

                    $this->report($this->position, '"\\t"');
                }
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position228;

                $_success = $this->parseEOL();
            }

            $this->cut = $_cut229;

            if (!$_success) {
                break;
            }

            $_value231[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position230;
            $this->value = $_value231;
        }

        $this->cut = $_cut232;

        $this->cache['VOID'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'VOID');
        }

        return $_success;
    }

    private function line()
    {
        if (!empty($this->errors)) {
            $positions = array_keys($this->errors);
        } else {
            $positions = array_keys($this->warnings);
        }

        return count(explode("\n", substr($this->string, 0, max($positions))));
    }

    private function rest()
    {
        return '"' . substr($this->string, $this->position) . '"';
    }

    protected function report($position, $expecting)
    {
        if ($this->cut) {
            $this->errors[$position][] = $expecting;
        } else {
            $this->warnings[$position][] = $expecting;
        }
    }

    private function expecting()
    {
        if (!empty($this->errors)) {
            ksort($this->errors);

            return end($this->errors)[0];
        }

        ksort($this->warnings);

        return implode(', ', end($this->warnings));
    }

    public function parse($_string)
    {
        $this->string = $_string;
        $this->position = 0;
        $this->value = null;
        $this->cache = array();
        $this->cut = false;
        $this->errors = array();
        $this->warnings = array();

        $_success = $this->parseFILE();

        if ($_success && $this->position < strlen($this->string)) {
            $_success = false;

            $this->report($this->position, "end of file");
        }

        if (!$_success) {
            throw new \InvalidArgumentException("Syntax error, expecting {$this->expecting()} on line {$this->line()}");
        }

        return $this->value;
    }
}