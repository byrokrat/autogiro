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

class Grammar
{
    protected $string;
    protected $position;
    protected $value;
    protected $cache;
    protected $cut;
    protected $errors;
    protected $warnings;

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

            $this->cut = $_cut2;

            if ($_success) {
                $file = $this->value;
            }
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

        $_value52 = array();

        if (substr($this->string, $this->position, strlen('82')) === '82') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('82'));
            $this->position += strlen('82');
        } else {
            $_success = false;

            $this->report($this->position, '\'82\'');
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseMSG1();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value52[] = $this->value;

            $this->value = $_value52;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                $status->setAttribute('message_id', Layouts::LAYOUT_PAYMENT_RESPONSE . '.' . $status->getValue());
                return new Response\IncomingPaymentResponse(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'interval' => $ival,
                        'repetitions' => $reps,
                        'payer_number' => $payerNr,
                        'amount' => $amount,
                        'payee_bankgiro' => $bg,
                        'reference' => $ref,
                        'status' => $status,
                    ]
                );
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

        $_value56 = array();

        $_success = $this->parsePAYMENT_OUTGOING_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_value54 = array();
            $_cut55 = $this->cut;

            while (true) {
                $_position53 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_OUTGOING_REC();

                if (!$_success) {
                    break;
                }

                $_value54[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position53;
                $this->value = $_value54;
            }

            $this->cut = $_cut55;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $this->value = $_value56;
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

        $_value57 = array();

        if (substr($this->string, $this->position, strlen('16')) === '16') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('16'));
            $this->position += strlen('16');
        } else {
            $_success = false;

            $this->report($this->position, '\'16\'');
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value57[] = $this->value;

            $this->value = $_value57;
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

        $_value58 = array();

        if (substr($this->string, $this->position, strlen('32')) === '32') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('32'));
            $this->position += strlen('32');
        } else {
            $_success = false;

            $this->report($this->position, '\'32\'');
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseMSG1();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $this->value = $_value58;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                $status->setAttribute('message_id', Layouts::LAYOUT_PAYMENT_RESPONSE . '.' . $status->getValue());
                return new Response\OutgoingPaymentResponse(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'interval' => $ival,
                        'repetitions' => $reps,
                        'payer_number' => $payerNr,
                        'amount' => $amount,
                        'payee_bankgiro' => $bg,
                        'reference' => $ref,
                        'status' => $status,
                    ]
                );
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

        $_value62 = array();

        $_success = $this->parsePAYMENT_REFUND_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_value60 = array();
            $_cut61 = $this->cut;

            while (true) {
                $_position59 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_REFUND_REC();

                if (!$_success) {
                    break;
                }

                $_value60[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position59;
                $this->value = $_value60;
            }

            $this->cut = $_cut61;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $this->value = $_value62;
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

        $_value63 = array();

        if (substr($this->string, $this->position, strlen('17')) === '17') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('17'));
            $this->position += strlen('17');
        } else {
            $_success = false;

            $this->report($this->position, '\'17\'');
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value63[] = $this->value;

            $this->value = $_value63;
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

        $_value64 = array();

        if (substr($this->string, $this->position, strlen('77')) === '77') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('77'));
            $this->position += strlen('77');
        } else {
            $_success = false;

            $this->report($this->position, '\'77\'');
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $refundDate = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $this->value = $_value64;
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

        $_value70 = array();

        $_success = $this->parseOLD_PAYMENT_OPENING();

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
                $_position65 = $this->position;
                $_cut66 = $this->cut;

                $this->cut = false;
                $_success = $this->parsePAYMENT_INCOMING_REC();

                if (!$_success && !$this->cut) {
                    $this->position = $_position65;

                    $_success = $this->parsePAYMENT_OUTGOING_REC();
                }

                $this->cut = $_cut66;

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
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseOLD_PAYMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value70[] = $this->value;

            $this->value = $_value70;
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

        $_value71 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
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
            $_value71[] = $this->value;

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
            $_value71[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
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

        $_value72 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
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
            $_value72[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

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
            $_value72[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value72[] = $this->value;

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
            $_value72[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value72[] = $this->value;

            $this->value = $_value72;
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

        $_value78 = array();

        $_position73 = $this->position;
        $_cut74 = $this->cut;

        $this->cut = false;
        $_success = $this->parseOLD_MANDATE_OPENING_REC();

        if (!$_success && !$this->cut) {
            $this->position = $_position73;

            $_success = $this->parseMANDATE_OPENING_REC();
        }

        $this->cut = $_cut74;

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
                $_success = $this->parseMANDATE_REC();

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
                $mands = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseMANDATE_CLOSING_REC();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $this->value = $_value78;
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

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS4();
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

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value79[] = $this->value;

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
            $_value79[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS();
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

        $_value80 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
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

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

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
            $_value80[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
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

        $_value87 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
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

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseACCOUNT16();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_position81 = $this->position;
            $_cut82 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS5();

            if (!$_success && !$this->cut) {
                $this->position = $_position81;

                if (substr($this->string, $this->position, strlen('00000')) === '00000') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen('00000'));
                    $this->position += strlen('00000');
                } else {
                    $_success = false;

                    $this->report($this->position, '\'00000\'');
                }
            }

            $this->cut = $_cut82;
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
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

            $_position86 = $this->position;

            $_position84 = $this->position;
            $_cut85 = $this->cut;

            $this->cut = false;
            $_value83 = array();

            $_success = $this->parseA5();

            if ($_success) {
                $_value83[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value83[] = $this->value;

                $this->value = $_value83;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position84;
                $this->value = null;
            }

            $this->cut = $_cut85;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position86, $this->position - $_position86));
            }

            if ($_success) {
                $validDate = $this->value;
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

        $_value88 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
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

            $_success = $this->parseINT7();

            if ($_success) {
                $nrRecs = $this->value;
            }
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

        $_value94 = array();

        $_position89 = $this->position;
        $_cut90 = $this->cut;

        $this->cut = false;
        $_success = $this->parsePAYMENT_REJECTION_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position89;

            $_success = $this->parseOLD_PAYMENT_REJECTION_OPENING();
        }

        $this->cut = $_cut90;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value94[] = $this->value;

            $_value92 = array();
            $_cut93 = $this->cut;

            while (true) {
                $_position91 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_REJECTION_RECORD();

                if (!$_success) {
                    break;
                }

                $_value92[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position91;
                $this->value = $_value92;
            }

            $this->cut = $_cut93;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value94[] = $this->value;

            $_success = $this->parsePAYMENT_REJECTION_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value94[] = $this->value;

            $this->value = $_value94;
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

        $_value95 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value95[] = $this->value;

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
            $_value95[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseS4();
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

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value95[] = $this->value;

            if (substr($this->string, $this->position, strlen('AVVISADE BET UPPDR')) === 'AVVISADE BET UPPDR') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AVVISADE BET UPPDR'));
                $this->position += strlen('AVVISADE BET UPPDR');
            } else {
                $_success = false;

                $this->report($this->position, '\'AVVISADE BET UPPDR\'');
            }
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
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

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value95[] = $this->value;

            $this->value = $_value95;
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

        $_value96 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
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
            $_value96[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value96[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value96[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value96[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
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

        $_value99 = array();

        $_position97 = $this->position;
        $_cut98 = $this->cut;

        $this->cut = false;
        $_success = $this->parsePAYMENT_REJECTION_INCOMING();

        if (!$_success && !$this->cut) {
            $this->position = $_position97;

            $_success = $this->parsePAYMENT_REJECTION_OUTGOING();
        }

        $this->cut = $_cut98;

        if ($_success) {
            $type = $this->value;
        }

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value99[] = $this->value;

            $this->value = $_value99;
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

        $_value100 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value100[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value100[] = $this->value;

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
            $_value100[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value100[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value100[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value100[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value100[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value100[] = $this->value;

            $this->value = $_value100;
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

    protected function parseACCOUNT16()
    {
        $_position = $this->position;

        if (isset($this->cache['ACCOUNT16'][$_position])) {
            $_success = $this->cache['ACCOUNT16'][$_position]['success'];
            $this->position = $this->cache['ACCOUNT16'][$_position]['position'];
            $this->value = $this->cache['ACCOUNT16'][$_position]['value'];

            return $_success;
        }

        $_position102 = $this->position;

        $_value101 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value101[] = $this->value;

            $this->value = $_value101;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position102, $this->position - $_position102));
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

        $_position104 = $this->position;

        $_value103 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value103[] = $this->value;

            $this->value = $_value103;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position104, $this->position - $_position104));
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

        $_position106 = $this->position;

        $_value105 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value105[] = $this->value;

            $this->value = $_value105;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position106, $this->position - $_position106));
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

        $_position108 = $this->position;

        $_value107 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value107[] = $this->value;

            $this->value = $_value107;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position108, $this->position - $_position108));
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

        $_position110 = $this->position;

        $_value109 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value109[] = $this->value;

            $this->value = $_value109;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position110, $this->position - $_position110));
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

        $_position112 = $this->position;

        $_value111 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value111[] = $this->value;

            $this->value = $_value111;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position112, $this->position - $_position112));
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

        $_position114 = $this->position;

        $_value113 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value113[] = $this->value;

            $this->value = $_value113;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position114, $this->position - $_position114));
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

        $_position116 = $this->position;

        $_value115 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value115[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value115[] = $this->value;

            $this->value = $_value115;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position116, $this->position - $_position116));
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

        $_position117 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position117, $this->position - $_position117));
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

        $_position118 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position118, $this->position - $_position118));
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

        $_position120 = $this->position;

        $_value119 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value119[] = $this->value;

            $this->value = $_value119;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position120, $this->position - $_position120));
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

        $_position122 = $this->position;

        $_value121 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value121[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value121[] = $this->value;

            $this->value = $_value121;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position122, $this->position - $_position122));
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

        $_position124 = $this->position;

        $_value123 = array();

        $_success = $this->parseA2();

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

        $_position125 = $this->position;

        $_success = $this->parseA5();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position125, $this->position - $_position125));
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

        $_position127 = $this->position;

        $_value126 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value126[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value126[] = $this->value;

            $this->value = $_value126;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position127, $this->position - $_position127));
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

        $_position129 = $this->position;

        $_value128 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value128[] = $this->value;

            $this->value = $_value128;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position129, $this->position - $_position129));
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

        $_position131 = $this->position;

        $_value130 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value130[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value130[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value130[] = $this->value;

            $this->value = $_value130;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position131, $this->position - $_position131));
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

        $_position133 = $this->position;

        $_value132 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value132[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value132[] = $this->value;

            $this->value = $_value132;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position133, $this->position - $_position133));
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

        $_position137 = $this->position;

        $_value135 = array();
        $_cut136 = $this->cut;

        while (true) {
            $_position134 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success) {
                break;
            }

            $_value135[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position134;
            $this->value = $_value135;
        }

        $this->cut = $_cut136;

        if ($_success) {
            $this->value = strval(substr($this->string, $_position137, $this->position - $_position137));
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

        $_position139 = $this->position;

        $_value138 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value138[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value138[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value138[] = $this->value;

            $this->value = $_value138;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position139, $this->position - $_position139));
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

        $_position141 = $this->position;

        $_value140 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value140[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value140[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value140[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value140[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value140[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value140[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value140[] = $this->value;

            $this->value = $_value140;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position141, $this->position - $_position141));
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

        if (preg_match('/^[a-zA-Z0-9 \\/&åäöÅÄÖ-]$/', substr($this->string, $this->position, 1))) {
            $_success = true;
            $this->value = substr($this->string, $this->position, 1);
            $this->position += 1;
        } else {
            $_success = false;
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

        $_position143 = $this->position;

        $_value142 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value142[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value142[] = $this->value;

            $this->value = $_value142;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position143, $this->position - $_position143));
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

    protected function parseA5()
    {
        $_position = $this->position;

        if (isset($this->cache['A5'][$_position])) {
            $_success = $this->cache['A5'][$_position]['success'];
            $this->position = $this->cache['A5'][$_position]['position'];
            $this->value = $this->cache['A5'][$_position]['value'];

            return $_success;
        }

        $_position145 = $this->position;

        $_value144 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value144[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value144[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value144[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value144[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value144[] = $this->value;

            $this->value = $_value144;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position145, $this->position - $_position145));
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

    protected function parseA10()
    {
        $_position = $this->position;

        if (isset($this->cache['A10'][$_position])) {
            $_success = $this->cache['A10'][$_position]['success'];
            $this->position = $this->cache['A10'][$_position]['position'];
            $this->value = $this->cache['A10'][$_position]['value'];

            return $_success;
        }

        $_position147 = $this->position;

        $_value146 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value146[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value146[] = $this->value;

            $this->value = $_value146;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position147, $this->position - $_position147));
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

    protected function parseS2()
    {
        $_position = $this->position;

        if (isset($this->cache['S2'][$_position])) {
            $_success = $this->cache['S2'][$_position]['success'];
            $this->position = $this->cache['S2'][$_position]['position'];
            $this->value = $this->cache['S2'][$_position]['value'];

            return $_success;
        }

        $_position149 = $this->position;

        $_value148 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value148[] = $this->value;

            $this->value = $_value148;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position149, $this->position - $_position149));
        }

        $this->cache['S2'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'S2');
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

        $_position151 = $this->position;

        $_value150 = array();

        $_success = $this->parseS2();

        if ($_success) {
            $_value150[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value150[] = $this->value;

            $this->value = $_value150;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position151, $this->position - $_position151));
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

        $_position153 = $this->position;

        $_value152 = array();

        $_success = $this->parseS4();

        if ($_success) {
            $_value152[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value152[] = $this->value;

            $this->value = $_value152;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position153, $this->position - $_position153));
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

        $_position155 = $this->position;

        $_value154 = array();

        $_success = $this->parseS5();

        if ($_success) {
            $_value154[] = $this->value;

            $_success = $this->parseS5();
        }

        if ($_success) {
            $_value154[] = $this->value;

            $this->value = $_value154;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position155, $this->position - $_position155));
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

        $_position157 = $this->position;

        $_value156 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value156[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value156[] = $this->value;

            $this->value = $_value156;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position157, $this->position - $_position157));
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

        $_value163 = array();

        $_value159 = array();
        $_cut160 = $this->cut;

        while (true) {
            $_position158 = $this->position;

            $this->cut = false;
            $_success = $this->parseS();

            if (!$_success) {
                break;
            }

            $_value159[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position158;
            $this->value = $_value159;
        }

        $this->cut = $_cut160;

        if ($_success) {
            $_value163[] = $this->value;

            $_position161 = $this->position;
            $_cut162 = $this->cut;

            $this->cut = false;
            $_success = $this->parseEOL();

            if (!$_success && !$this->cut) {
                $this->position = $_position161;

                $_success = $this->parseEOF();
            }

            $this->cut = $_cut162;
        }

        if ($_success) {
            $_value163[] = $this->value;

            $this->value = $_value163;
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

        $_value166 = array();

        $_position164 = $this->position;
        $_cut165 = $this->cut;

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
            $this->position = $_position164;
            $this->value = null;
        }

        $this->cut = $_cut165;

        if ($_success) {
            $_value166[] = $this->value;

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
            $_value166[] = $this->value;

            $this->value = $_value166;
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

        $_position167 = $this->position;
        $_cut168 = $this->cut;

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

        $this->position = $_position167;
        $this->cut = $_cut168;

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