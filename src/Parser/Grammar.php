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

                $_success = $this->parseRESP_MANDATE_FILE();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parseRESP_PAYMENT_FILE();
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
        if (substr($this->string, $this->position, strlen('32')) === '32') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('32'));
            $this->position += strlen('32');
        } else {
            $_success = false;

            $this->report($this->position, '\'32\'');
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position28;

            if (substr($this->string, $this->position, strlen('82')) === '82') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('82'));
                $this->position += strlen('82');
            } else {
                $_success = false;

                $this->report($this->position, '\'82\'');
            }
        }

        $this->cut = $_cut29;

        if ($_success) {
            $tc = $this->value;
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
            $this->value = call_user_func(function () use (&$tc, &$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref) {
                $tc2record = [
                    '32' => Request\OutgoingPaymentRequest::CLASS,
                    '82' => Request\IncomingPaymentRequest::CLASS,
                ];

                return new $tc2record[$tc](
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

    protected function parseRESP_PAYMENT_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_FILE'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_FILE'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_FILE'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_FILE'][$_position]['value'];

            return $_success;
        }

        $_value42 = array();

        $_success = $this->parseRESP_PAYMENT_OPENING_REC();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value42[] = $this->value;

            $_position37 = $this->position;
            $_cut38 = $this->cut;

            $this->cut = false;
            $_success = $this->parseRESP_PAYMENT_INCOMING_SECTION();

            if (!$_success && !$this->cut) {
                $this->position = $_position37;

                $_success = $this->parseRESP_PAYMENT_OUTGOING_SECTION();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position37;

                $_success = $this->parseRESP_PAYMENT_REFUND_SECTION();
            }

            $this->cut = $_cut38;

            if ($_success) {
                $_value40 = array($this->value);
                $_cut41 = $this->cut;

                while (true) {
                    $_position39 = $this->position;

                    $this->cut = false;
                    $_position37 = $this->position;
                    $_cut38 = $this->cut;

                    $this->cut = false;
                    $_success = $this->parseRESP_PAYMENT_INCOMING_SECTION();

                    if (!$_success && !$this->cut) {
                        $this->position = $_position37;

                        $_success = $this->parseRESP_PAYMENT_OUTGOING_SECTION();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position37;

                        $_success = $this->parseRESP_PAYMENT_REFUND_SECTION();
                    }

                    $this->cut = $_cut38;

                    if (!$_success) {
                        break;
                    }

                    $_value40[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position39;
                    $this->value = $_value40;
                }

                $this->cut = $_cut41;
            }

            if ($_success) {
                $sections = $this->value;
            }
        }

        if ($_success) {
            $_value42[] = $this->value;

            $_success = $this->parseRESP_PAYMENT_CLOSING_REC();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value42[] = $this->value;

            $this->value = $_value42;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$sections, &$close) {
                $sections[] = $close;
                return new FileNode(Layouts::LAYOUT_PAYMENT_RESPONSE, $open, ...$sections);
            });
        }

        $this->cache['RESP_PAYMENT_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_FILE');
        }

        return $_success;
    }

    protected function parseRESP_PAYMENT_OPENING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_OPENING_REC'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_OPENING_REC'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_OPENING_REC'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_OPENING_REC'][$_position]['value'];

            return $_success;
        }

        $_value43 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value43[] = $this->value;

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
            $_value43[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseDATETIME();

            if ($_success) {
                $datetime = $this->value;
            }
        }

        if ($_success) {
            $_value43[] = $this->value;

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
            $_value43[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value43[] = $this->value;

            $this->value = $_value43;
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

        $this->cache['RESP_PAYMENT_OPENING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_OPENING_REC');
        }

        return $_success;
    }

    protected function parseRESP_PAYMENT_CLOSING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_CLOSING_REC'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_CLOSING_REC'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_CLOSING_REC'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_CLOSING_REC'][$_position]['value'];

            return $_success;
        }

        $_value44 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

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
            $_value44[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrInLays = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrInRecs = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOutLays = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrOutRecs = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrRefLays = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrRefRecs = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value44[] = $this->value;

            $this->value = $_value44;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrInLays, &$nrInRecs, &$nrOutLays, &$nrOutRecs, &$nrRefLays, &$nrRefRecs) {
                return new Response\PaymentResponseClosingRecord($this->lineNr, $date, $nrInLays, $nrInRecs, $nrOutLays, $nrOutRecs, $nrRefLays, $nrRefRecs);
            });
        }

        $this->cache['RESP_PAYMENT_CLOSING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_CLOSING_REC');
        }

        return $_success;
    }

    protected function parseRESP_PAYMENT_INCOMING_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_INCOMING_SECTION'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_INCOMING_SECTION'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_INCOMING_SECTION'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_INCOMING_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value48 = array();

        $_success = $this->parseRESP_PAYMENT_SECTION_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value48[] = $this->value;

            $_value46 = array();
            $_cut47 = $this->cut;

            while (true) {
                $_position45 = $this->position;

                $this->cut = false;
                $_success = $this->parseRESP_PAYMENT_REC();

                if (!$_success) {
                    break;
                }

                $_value46[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position45;
                $this->value = $_value46;
            }

            $this->cut = $_cut47;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value48[] = $this->value;

            $this->value = $_value48;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Response\IncomingPaymentResponseSection($open, ...$records);
            });
        }

        $this->cache['RESP_PAYMENT_INCOMING_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_INCOMING_SECTION');
        }

        return $_success;
    }

    protected function parseRESP_PAYMENT_OUTGOING_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_OUTGOING_SECTION'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_OUTGOING_SECTION'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_OUTGOING_SECTION'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_OUTGOING_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value52 = array();

        $_success = $this->parseRESP_PAYMENT_SECTION_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value52[] = $this->value;

            $_value50 = array();
            $_cut51 = $this->cut;

            while (true) {
                $_position49 = $this->position;

                $this->cut = false;
                $_success = $this->parseRESP_PAYMENT_REC();

                if (!$_success) {
                    break;
                }

                $_value50[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position49;
                $this->value = $_value50;
            }

            $this->cut = $_cut51;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value52[] = $this->value;

            $this->value = $_value52;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Response\OutgoingPaymentResponseSection('', $open, ...$records);
            });
        }

        $this->cache['RESP_PAYMENT_OUTGOING_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_OUTGOING_SECTION');
        }

        return $_success;
    }

    protected function parseRESP_PAYMENT_REFUND_SECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_REFUND_SECTION'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_REFUND_SECTION'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_REFUND_SECTION'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_REFUND_SECTION'][$_position]['value'];

            return $_success;
        }

        $_value56 = array();

        $_success = $this->parseRESP_PAYMENT_SECTION_OPENING();

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
                $_success = $this->parseRESP_PAYMENT_REFUND_REC();

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
                return new Response\RefundPaymentResponseSection('', $open, ...$records);
            });
        }

        $this->cache['RESP_PAYMENT_REFUND_SECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_REFUND_SECTION');
        }

        return $_success;
    }

    protected function parseRESP_PAYMENT_SECTION_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_SECTION_OPENING'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_SECTION_OPENING'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_SECTION_OPENING'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_SECTION_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value59 = array();

        $_position57 = $this->position;
        $_cut58 = $this->cut;

        $this->cut = false;
        if (substr($this->string, $this->position, strlen('15')) === '15') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('15'));
            $this->position += strlen('15');
        } else {
            $_success = false;

            $this->report($this->position, '\'15\'');
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position57;

            if (substr($this->string, $this->position, strlen('16')) === '16') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('16'));
                $this->position += strlen('16');
            } else {
                $_success = false;

                $this->report($this->position, '\'16\'');
            }
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position57;

            if (substr($this->string, $this->position, strlen('17')) === '17') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('17'));
                $this->position += strlen('17');
            } else {
                $_success = false;

                $this->report($this->position, '\'17\'');
            }
        }

        $this->cut = $_cut58;

        if ($_success) {
            $tc = $this->value;
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value59[] = $this->value;

            $this->value = $_value59;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$tc, &$account, &$date, &$serial, &$amount, &$nrRecs) {
                $tc2record = [
                    '15' => Response\IncomingPaymentResponseOpening::CLASS,
                    '16' => Response\OutgoingPaymentResponseOpening::CLASS,
                    '17' => Response\RefundPaymentResponseOpening::CLASS,
                ];

                return new $tc2record[$tc](
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

        $this->cache['RESP_PAYMENT_SECTION_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_SECTION_OPENING');
        }

        return $_success;
    }

    protected function parseRESP_PAYMENT_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_REC'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_REC'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_REC'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_REC'][$_position]['value'];

            return $_success;
        }

        $_value62 = array();

        $_position60 = $this->position;
        $_cut61 = $this->cut;

        $this->cut = false;
        if (substr($this->string, $this->position, strlen('82')) === '82') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('82'));
            $this->position += strlen('82');
        } else {
            $_success = false;

            $this->report($this->position, '\'82\'');
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position60;

            if (substr($this->string, $this->position, strlen('32')) === '32') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('32'));
                $this->position += strlen('32');
            } else {
                $_success = false;

                $this->report($this->position, '\'32\'');
            }
        }

        $this->cut = $_cut61;

        if ($_success) {
            $tc = $this->value;
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseMSG1();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value62[] = $this->value;

            $this->value = $_value62;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$tc, &$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                $tc2record = [
                    '32' => Response\OutgoingPaymentResponse::CLASS,
                    '82' => Response\IncomingPaymentResponse::CLASS,
                ];

                return new $tc2record[$tc](
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

        $this->cache['RESP_PAYMENT_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_REC');
        }

        return $_success;
    }

    protected function parseRESP_PAYMENT_REFUND_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_REFUND_REC'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_REFUND_REC'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_REFUND_REC'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_REFUND_REC'][$_position]['value'];

            return $_success;
        }

        $_value63 = array();

        if (substr($this->string, $this->position, strlen('77')) === '77') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('77'));
            $this->position += strlen('77');
        } else {
            $_success = false;

            $this->report($this->position, '\'77\'');
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

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $refundDate = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
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
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$refundDate, &$status) {
                // TODO här är jag. Varför lyckas jag inte matcha den när raden???
                return new Response\RefundPaymentResponse($this->lineNr, $date, $ival, $reps, $payerNr, $amount, $bg, $ref, $refundDate, $status);
            });
        }

        $this->cache['RESP_PAYMENT_REFUND_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_REFUND_REC');
        }

        return $_success;
    }

    protected function parseRESP_MANDATE_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_FILE'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_FILE'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_FILE'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_FILE'][$_position]['value'];

            return $_success;
        }

        $_value69 = array();

        $_position64 = $this->position;
        $_cut65 = $this->cut;

        $this->cut = false;
        $_success = $this->parseRESP_MANDATE_OPENING_OLD_REC();

        if (!$_success && !$this->cut) {
            $this->position = $_position64;

            $_success = $this->parseRESP_MANDATE_OPENING_REC();
        }

        $this->cut = $_cut65;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_value67 = array();
            $_cut68 = $this->cut;

            while (true) {
                $_position66 = $this->position;

                $this->cut = false;
                $_success = $this->parseRESP_MANDATE_REC();

                if (!$_success) {
                    break;
                }

                $_value67[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position66;
                $this->value = $_value67;
            }

            $this->cut = $_cut68;

            if ($_success) {
                $mands = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseRESP_MANDATE_CLOSING_REC();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $this->value = $_value69;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$mands, &$close) {
                $mands[] = $close;
                return new FileNode(Layouts::LAYOUT_MANDATE_RESPONSE, $open, ...$mands);
            });
        }

        $this->cache['RESP_MANDATE_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_FILE');
        }

        return $_success;
    }

    protected function parseRESP_MANDATE_OPENING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_OPENING_REC'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_OPENING_REC'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_OPENING_REC'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_OPENING_REC'][$_position]['value'];

            return $_success;
        }

        $_value70 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value70[] = $this->value;

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
            $_value70[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value70[] = $this->value;

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
            $_value70[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $this->value = $_value70;
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

        $this->cache['RESP_MANDATE_OPENING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_OPENING_REC');
        }

        return $_success;
    }

    protected function parseRESP_MANDATE_OPENING_OLD_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_OPENING_OLD_REC'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_OPENING_OLD_REC'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_OPENING_OLD_REC'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_OPENING_OLD_REC'][$_position]['value'];

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

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value71[] = $this->value;

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
            $_value71[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
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

        $this->cache['RESP_MANDATE_OPENING_OLD_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_OPENING_OLD_REC');
        }

        return $_success;
    }

    protected function parseRESP_MANDATE_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_REC'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_REC'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_REC'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_REC'][$_position]['value'];

            return $_success;
        }

        $_value78 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseACCOUNT16();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_position72 = $this->position;
            $_cut73 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS5();

            if (!$_success && !$this->cut) {
                $this->position = $_position72;

                if (substr($this->string, $this->position, strlen('00000')) === '00000') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen('00000'));
                    $this->position += strlen('00000');
                } else {
                    $_success = false;

                    $this->report($this->position, '\'00000\'');
                }
            }

            $this->cut = $_cut73;
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_position77 = $this->position;

            $_position75 = $this->position;
            $_cut76 = $this->cut;

            $this->cut = false;
            $_value74 = array();

            $_success = $this->parseA5();

            if ($_success) {
                $_value74[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value74[] = $this->value;

                $this->value = $_value74;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position75;
                $this->value = null;
            }

            $this->cut = $_cut76;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position77, $this->position - $_position77));
            }

            if ($_success) {
                $validDate = $this->value;
            }
        }

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value78[] = $this->value;

            $this->value = $_value78;
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

        $this->cache['RESP_MANDATE_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_REC');
        }

        return $_success;
    }

    protected function parseRESP_MANDATE_CLOSING_REC()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_CLOSING_REC'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_CLOSING_REC'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_CLOSING_REC'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_CLOSING_REC'][$_position]['value'];

            return $_success;
        }

        $_value79 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
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

            $_success = $this->parseINT7();

            if ($_success) {
                $nrOfPosts = $this->value;
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
            $this->value = call_user_func(function () use (&$date, &$nrOfPosts) {
                return new Response\MandateResponseClosing(
                    $this->lineNr,
                    [
                        'date' => $date,
                        'nr_of_posts' => $nrOfPosts,
                    ]
                );
            });
        }

        $this->cache['RESP_MANDATE_CLOSING_REC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_CLOSING_REC');
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

        $_position81 = $this->position;

        $_value80 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $this->value = $_value80;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position81, $this->position - $_position81));
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

        $_position83 = $this->position;

        $_value82 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $this->value = $_value82;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position83, $this->position - $_position83));
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

        $_position85 = $this->position;

        $_value84 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value84[] = $this->value;

            $this->value = $_value84;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position85, $this->position - $_position85));
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

        $_position87 = $this->position;

        $_value86 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value86[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value86[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value86[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value86[] = $this->value;

            $this->value = $_value86;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position87, $this->position - $_position87));
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

        $_position89 = $this->position;

        $_value88 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value88[] = $this->value;

            $this->value = $_value88;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position89, $this->position - $_position89));
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

        $_position91 = $this->position;

        $_value90 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value90[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value90[] = $this->value;

            $this->value = $_value90;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position91, $this->position - $_position91));
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

        $_position93 = $this->position;

        $_value92 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value92[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value92[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value92[] = $this->value;

            $this->value = $_value92;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position93, $this->position - $_position93));
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

        $_position95 = $this->position;

        $_value94 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value94[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value94[] = $this->value;

            $this->value = $_value94;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position95, $this->position - $_position95));
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

        $_position96 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position96, $this->position - $_position96));
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

        $_position97 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position97, $this->position - $_position97));
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

        $_position99 = $this->position;

        $_value98 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value98[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value98[] = $this->value;

            $this->value = $_value98;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position99, $this->position - $_position99));
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

        $_position101 = $this->position;

        $_value100 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value100[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value100[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value100[] = $this->value;

            $this->value = $_value100;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position101, $this->position - $_position101));
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

        $_position103 = $this->position;

        $_value102 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value102[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value102[] = $this->value;

            $this->value = $_value102;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position103, $this->position - $_position103));
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

        $_position104 = $this->position;

        $_success = $this->parseA5();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position104, $this->position - $_position104));
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

        $_position106 = $this->position;

        $_value105 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value105[] = $this->value;

            $this->value = $_value105;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position106, $this->position - $_position106));
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

        $_position108 = $this->position;

        $_value107 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value107[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value107[] = $this->value;

            $this->value = $_value107;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position108, $this->position - $_position108));
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

        $_position110 = $this->position;

        $_value109 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value109[] = $this->value;

            $this->value = $_value109;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position110, $this->position - $_position110));
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

        $_position112 = $this->position;

        $_value111 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value111[] = $this->value;

            $this->value = $_value111;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position112, $this->position - $_position112));
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

        $_position116 = $this->position;

        $_value114 = array();
        $_cut115 = $this->cut;

        while (true) {
            $_position113 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

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
            $this->value = strval(substr($this->string, $_position116, $this->position - $_position116));
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

        $_position118 = $this->position;

        $_value117 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value117[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value117[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value117[] = $this->value;

            $this->value = $_value117;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position118, $this->position - $_position118));
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

        $_position120 = $this->position;

        $_value119 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseA2();
        }

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

        $_position122 = $this->position;

        $_value121 = array();

        $_success = $this->parseA();

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

        $_position124 = $this->position;

        $_value123 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseA();
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

        $_position126 = $this->position;

        $_value125 = array();

        $_success = $this->parseA5();

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

        $_position128 = $this->position;

        $_value127 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value127[] = $this->value;

            $this->value = $_value127;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position128, $this->position - $_position128));
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

        $_position130 = $this->position;

        $_value129 = array();

        $_success = $this->parseS2();

        if ($_success) {
            $_value129[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value129[] = $this->value;

            $this->value = $_value129;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position130, $this->position - $_position130));
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

        $_position132 = $this->position;

        $_value131 = array();

        $_success = $this->parseS4();

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value131[] = $this->value;

            $this->value = $_value131;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position132, $this->position - $_position132));
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

        $_position134 = $this->position;

        $_value133 = array();

        $_success = $this->parseS5();

        if ($_success) {
            $_value133[] = $this->value;

            $_success = $this->parseS5();
        }

        if ($_success) {
            $_value133[] = $this->value;

            $this->value = $_value133;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position134, $this->position - $_position134));
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

        $_position136 = $this->position;

        $_value135 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value135[] = $this->value;

            $this->value = $_value135;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position136, $this->position - $_position136));
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

        $_value142 = array();

        $_value138 = array();
        $_cut139 = $this->cut;

        while (true) {
            $_position137 = $this->position;

            $this->cut = false;
            $_success = $this->parseS();

            if (!$_success) {
                break;
            }

            $_value138[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position137;
            $this->value = $_value138;
        }

        $this->cut = $_cut139;

        if ($_success) {
            $_value142[] = $this->value;

            $_position140 = $this->position;
            $_cut141 = $this->cut;

            $this->cut = false;
            $_success = $this->parseEOL();

            if (!$_success && !$this->cut) {
                $this->position = $_position140;

                $_success = $this->parseEOF();
            }

            $this->cut = $_cut141;
        }

        if ($_success) {
            $_value142[] = $this->value;

            $this->value = $_value142;
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

        $_value145 = array();

        $_position143 = $this->position;
        $_cut144 = $this->cut;

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
            $this->position = $_position143;
            $this->value = null;
        }

        $this->cut = $_cut144;

        if ($_success) {
            $_value145[] = $this->value;

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
            $_value145[] = $this->value;

            $this->value = $_value145;
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

        $_position146 = $this->position;
        $_cut147 = $this->cut;

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

        $this->position = $_position146;
        $this->cut = $_cut147;

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