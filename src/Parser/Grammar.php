<?php

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Exception\ContentException;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Container;
use byrokrat\autogiro\Tree\Count;
use byrokrat\autogiro\Tree\Date;
use byrokrat\autogiro\Tree\Flag;
use byrokrat\autogiro\Tree\ImmediateDate;
use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Tree\Message;
use byrokrat\autogiro\Tree\Number;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Tree\Section;
use byrokrat\autogiro\Tree\Summary;
use byrokrat\autogiro\Tree\Text;

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
                $this->lineNr = 1;
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
                return new AutogiroFile('AutogiroRequestFile', ...$sections);
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

    protected function parseREQ_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_OPENING'][$_position])) {
            $_success = $this->cache['REQ_OPENING'][$_position]['success'];
            $this->position = $this->cache['REQ_OPENING'][$_position]['position'];
            $this->value = $this->cache['REQ_OPENING'][$_position]['value'];

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

            $_success = $this->parseDATE8();

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
                return new Record('Opening', $date, $bgcNr, $bg);
            });
        }

        $this->cache['REQ_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_OPENING');
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

        $_success = $this->parseREQ_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value15[] = $this->value;

            $_position10 = $this->position;
            $_cut11 = $this->cut;

            $this->cut = false;
            $_success = $this->parseREQ_DEL_MANDATE();

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_REJECT_MANDATE();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_CREATE_MANDATE();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_UPDATE_MANDATE();
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
                    $_success = $this->parseREQ_DEL_MANDATE();

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_REJECT_MANDATE();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_CREATE_MANDATE();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_UPDATE_MANDATE();
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
                return new Section('MandateRequestSection', $open, ...$records);
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

    protected function parseREQ_DEL_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_DEL_MANDATE'][$_position])) {
            $_success = $this->cache['REQ_DEL_MANDATE'][$_position]['success'];
            $this->position = $this->cache['REQ_DEL_MANDATE'][$_position]['position'];
            $this->value = $this->cache['REQ_DEL_MANDATE'][$_position]['value'];

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
                return new Record('DeleteMandateRequest', $bg, $payerNr);
            });
        }

        $this->cache['REQ_DEL_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_DEL_MANDATE');
        }

        return $_success;
    }

    protected function parseREQ_REJECT_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_REJECT_MANDATE'][$_position])) {
            $_success = $this->cache['REQ_REJECT_MANDATE'][$_position]['success'];
            $this->position = $this->cache['REQ_REJECT_MANDATE'][$_position]['position'];
            $this->value = $this->cache['REQ_REJECT_MANDATE'][$_position]['value'];

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
                return new Record('RejectDigitalMandateRequest', $bg, $payerNr);
            });
        }

        $this->cache['REQ_REJECT_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_REJECT_MANDATE');
        }

        return $_success;
    }

    protected function parseREQ_CREATE_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_CREATE_MANDATE'][$_position])) {
            $_success = $this->cache['REQ_CREATE_MANDATE'][$_position]['success'];
            $this->position = $this->cache['REQ_CREATE_MANDATE'][$_position]['position'];
            $this->value = $this->cache['REQ_CREATE_MANDATE'][$_position]['value'];

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
                return $id && trim($id->getChild('Number')->getValue())
                    ? new Record('CreateMandateRequest', $bg, $payerNr, $account, $id)
                    : new Record('AcceptDigitalMandateRequest', $bg, $payerNr);
            });
        }

        $this->cache['REQ_CREATE_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_CREATE_MANDATE');
        }

        return $_success;
    }

    protected function parseREQ_UPDATE_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_UPDATE_MANDATE'][$_position])) {
            $_success = $this->cache['REQ_UPDATE_MANDATE'][$_position]['success'];
            $this->position = $this->cache['REQ_UPDATE_MANDATE'][$_position]['position'];
            $this->value = $this->cache['REQ_UPDATE_MANDATE'][$_position]['value'];

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
                return new Record('UpdateMandateRequest', $oldBg, $oldPayerNr, $newBg, $newPayerNr);
            });
        }

        $this->cache['REQ_UPDATE_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_UPDATE_MANDATE');
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

        $_success = $this->parseREQ_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parseREQ_PAYMENT();

            if ($_success) {
                $_value25 = array($this->value);
                $_cut26 = $this->cut;

                while (true) {
                    $_position24 = $this->position;

                    $this->cut = false;
                    $_success = $this->parseREQ_PAYMENT();

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
                return new Section('PaymentRequestSection', $open, ...$records);
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

    protected function parseREQ_PAYMENT()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_PAYMENT'][$_position])) {
            $_success = $this->cache['REQ_PAYMENT'][$_position]['success'];
            $this->position = $this->cache['REQ_PAYMENT'][$_position]['position'];
            $this->value = $this->cache['REQ_PAYMENT'][$_position]['value'];

            return $_success;
        }

        $_value32 = array();

        $_position28 = $this->position;
        $_cut29 = $this->cut;

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
            $this->position = $_position28;

            if (substr($this->string, $this->position, strlen('32')) === '32') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('32'));
                $this->position += strlen('32');
            } else {
                $_success = false;

                $this->report($this->position, '\'32\'');
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

                $_success = $this->parseDATE8();
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
                $types = [
                    '82' => 'IncomingPaymentRequest',
                    '32' => 'OutgoingPaymentRequest',
                ];

                return new Record($types[$tc], $date, $ival, $reps, $payerNr, $amount, $bg, $ref);
            });
        }

        $this->cache['REQ_PAYMENT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_PAYMENT');
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

        $_success = $this->parseREQ_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseREQ_REVOCATION();

            if ($_success) {
                $_value34 = array($this->value);
                $_cut35 = $this->cut;

                while (true) {
                    $_position33 = $this->position;

                    $this->cut = false;
                    $_success = $this->parseREQ_REVOCATION();

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
                return new Section('AmendmentRequestSection', $open, ...$records);
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

    protected function parseREQ_REVOCATION()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_REVOCATION'][$_position])) {
            $_success = $this->cache['REQ_REVOCATION'][$_position]['success'];
            $this->position = $this->cache['REQ_REVOCATION'][$_position]['position'];
            $this->value = $this->cache['REQ_REVOCATION'][$_position]['value'];

            return $_success;
        }

        $_value39 = array();

        $_position38 = $this->position;

        $_value37 = array();

        if (substr($this->string, $this->position, strlen('2')) === '2') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('2'));
            $this->position += strlen('2');
        } else {
            $_success = false;

            $this->report($this->position, '\'2\'');
        }

        if ($_success) {
            $_value37[] = $this->value;

            if (preg_match('/^[3-9]$/', substr($this->string, $this->position, 1))) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }
        }

        if ($_success) {
            $_value37[] = $this->value;

            $this->value = $_value37;
        }

        if ($_success) {
            $this->value = null;
        }

        $this->position = $_position38;

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $tc = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $type = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $newDate = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value39[] = $this->value;

            $this->value = $_value39;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$tc, &$bg, &$payerNr, &$date, &$amount, &$type, &$newDate, &$ref) {
                $tc->setAttribute('message_id', 'AutogiroRequestFile.TC.' . $tc->getValue());
                $newDate->setName('NewDate');
                return new Record('AmendmentRequest', $tc, $bg, $payerNr, $date, $amount, $type, $newDate, $ref);
            });
        }

        $this->cache['REQ_REVOCATION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_REVOCATION');
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

        $_position40 = $this->position;
        $_cut41 = $this->cut;

        $this->cut = false;
        $_success = $this->parseNEW_PAYMENT_FILE();

        if (!$_success && !$this->cut) {
            $this->position = $_position40;

            $_success = $this->parseOLD_PAYMENT_FILE();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position40;

            $_success = $this->parseBGMAX_FILE();
        }

        $this->cut = $_cut41;

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

        $_value47 = array();

        $_success = $this->parsePAYMENT_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value47[] = $this->value;

            $_position42 = $this->position;
            $_cut43 = $this->cut;

            $this->cut = false;
            $_success = $this->parsePAYMENT_INCOMING_SECTION();

            if (!$_success && !$this->cut) {
                $this->position = $_position42;

                $_success = $this->parsePAYMENT_OUTGOING_SECTION();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position42;

                $_success = $this->parsePAYMENT_REFUND_SECTION();
            }

            $this->cut = $_cut43;

            if ($_success) {
                $_value45 = array($this->value);
                $_cut46 = $this->cut;

                while (true) {
                    $_position44 = $this->position;

                    $this->cut = false;
                    $_position42 = $this->position;
                    $_cut43 = $this->cut;

                    $this->cut = false;
                    $_success = $this->parsePAYMENT_INCOMING_SECTION();

                    if (!$_success && !$this->cut) {
                        $this->position = $_position42;

                        $_success = $this->parsePAYMENT_OUTGOING_SECTION();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position42;

                        $_success = $this->parsePAYMENT_REFUND_SECTION();
                    }

                    $this->cut = $_cut43;

                    if (!$_success) {
                        break;
                    }

                    $_value45[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position44;
                    $this->value = $_value45;
                }

                $this->cut = $_cut46;
            }

            if ($_success) {
                $sections = $this->value;
            }
        }

        if ($_success) {
            $_value47[] = $this->value;

            $_success = $this->parsePAYMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value47[] = $this->value;

            $this->value = $_value47;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$sections, &$close) {
                $sections[] = $close;
                return new AutogiroFile('AutogiroPaymentResponseFile', $open, ...$sections);
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

    protected function parsePAYMENT_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_OPENING'][$_position])) {
            $_success = $this->cache['PAYMENT_OPENING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_OPENING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value48 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value48[] = $this->value;

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
            $_value48[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value48[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value48[] = $this->value;

            $_success = $this->parseDATE20();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value48[] = $this->value;

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
            $_value48[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value48[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value48[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value48[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value48[] = $this->value;

            $this->value = $_value48;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Record('Opening', $date, $bgcNr, $bg);
            });
        }

        $this->cache['PAYMENT_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_OPENING');
        }

        return $_success;
    }

    protected function parsePAYMENT_CLOSING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_CLOSING'][$_position])) {
            $_success = $this->cache['PAYMENT_CLOSING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_CLOSING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_CLOSING'][$_position]['value'];

            return $_success;
        }

        $_value49 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

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
            $_value49[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrInSecs = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrInRecs = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOutSecs = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrOutRecs = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrRefSecs = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrRefRecs = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value49[] = $this->value;

            $this->value = $_value49;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrInSecs, &$nrInRecs, &$nrOutSecs, &$nrOutRecs, &$nrRefSecs, &$nrRefRecs) {
                return new Record(
                    'Closing',
                    $date,
                    new Count('IncomingPaymentResponseSection', $nrInSecs),
                    new Count('SuccessfulIncomingPaymentResponse', $nrInRecs),
                    new Count('OutgoingPaymentResponseSection', $nrOutSecs),
                    new Count('SuccessfulOutgoingPaymentResponse', $nrOutRecs),
                    new Count('RefundPaymentResponseSection', $nrRefSecs),
                    new Count('RefundPaymentResponse', $nrRefRecs)
                );
            });
        }

        $this->cache['PAYMENT_CLOSING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_CLOSING');
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

        $_value53 = array();

        $_success = $this->parsePAYMENT_INCOMING_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value53[] = $this->value;

            $_value51 = array();
            $_cut52 = $this->cut;

            while (true) {
                $_position50 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_INCOMING();

                if (!$_success) {
                    break;
                }

                $_value51[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position50;
                $this->value = $_value51;
            }

            $this->cut = $_cut52;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value53[] = $this->value;

            $this->value = $_value53;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Section('IncomingPaymentResponseSection', $open, ...$records);
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

        $_value54 = array();

        if (substr($this->string, $this->position, strlen('15')) === '15') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('15'));
            $this->position += strlen('15');
        } else {
            $_success = false;

            $this->report($this->position, '\'15\'');
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value54[] = $this->value;

            $this->value = $_value54;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$account, &$date, &$serial, &$amount, &$nrRecs) {
                $nrRecs->setName('IncomingPaymentCount');
                return new Record('IncomingPaymentResponseSectionOpening', $account, $date, $serial, $amount, $nrRecs);
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

    protected function parsePAYMENT_INCOMING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_INCOMING'][$_position])) {
            $_success = $this->cache['PAYMENT_INCOMING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_INCOMING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_INCOMING'][$_position]['value'];

            return $_success;
        }

        $_value59 = array();

        if (substr($this->string, $this->position, strlen('82')) === '82') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('82'));
            $this->position += strlen('82');
        } else {
            $_success = false;

            $this->report($this->position, '\'82\'');
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_position55 = $this->position;
            $_cut56 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA10();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position55;
                $this->value = null;
            }

            $this->cut = $_cut56;
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_position57 = $this->position;
            $_cut58 = $this->cut;

            $this->cut = false;
            $_success = $this->parseMSG1();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position57;
                $this->value = null;
            }

            $this->cut = $_cut58;

            if ($_success) {
                $status = $this->value;
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
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                if ($status) {
                    $status->setAttribute('message_id', 'AutogiroPaymentResponseFile.' . $status->getValue());
                }

                $flag = !$status || $status->getValue() == '0' ? 'Successful' : 'Failed';

                return new Record($flag.'IncomingPaymentResponse', new Flag($flag), $date, $ival, $reps, $payerNr, $amount, $bg, $ref, $status);
            });
        }

        $this->cache['PAYMENT_INCOMING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_INCOMING');
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

        $_value63 = array();

        $_success = $this->parsePAYMENT_OUTGOING_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_value61 = array();
            $_cut62 = $this->cut;

            while (true) {
                $_position60 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_OUTGOING();

                if (!$_success) {
                    break;
                }

                $_value61[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position60;
                $this->value = $_value61;
            }

            $this->cut = $_cut62;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $this->value = $_value63;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Section('OutgoingPaymentResponseSection', $open, ...$records);
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

        $_value64 = array();

        if (substr($this->string, $this->position, strlen('16')) === '16') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('16'));
            $this->position += strlen('16');
        } else {
            $_success = false;

            $this->report($this->position, '\'16\'');
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
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
            $this->value = call_user_func(function () use (&$account, &$date, &$serial, &$amount, &$nrRecs) {
                $nrRecs->setName('IncomingPaymentCount');
                return new Record('OutgoingPaymentResponseSectionOpening', $account, $date, $serial, $amount, $nrRecs);
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

    protected function parsePAYMENT_OUTGOING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_OUTGOING'][$_position])) {
            $_success = $this->cache['PAYMENT_OUTGOING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_OUTGOING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_OUTGOING'][$_position]['value'];

            return $_success;
        }

        $_value69 = array();

        if (substr($this->string, $this->position, strlen('32')) === '32') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('32'));
            $this->position += strlen('32');
        } else {
            $_success = false;

            $this->report($this->position, '\'32\'');
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_position65 = $this->position;
            $_cut66 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA10();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position65;
                $this->value = null;
            }

            $this->cut = $_cut66;
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_position67 = $this->position;
            $_cut68 = $this->cut;

            $this->cut = false;
            $_success = $this->parseMSG1();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position67;
                $this->value = null;
            }

            $this->cut = $_cut68;

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value69[] = $this->value;

            $this->value = $_value69;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                if ($status) {
                    $status->setAttribute('message_id', 'AutogiroPaymentResponseFile.' . $status->getValue());
                }

                $flag = !$status || $status->getValue() == '0' ? 'Successful' : 'Failed';

                return new Record($flag.'OutgoingPaymentResponse', new Flag($flag), $date, $ival, $reps, $payerNr, $amount, $bg, $ref, $status);
            });
        }

        $this->cache['PAYMENT_OUTGOING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_OUTGOING');
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

        $_value73 = array();

        $_success = $this->parsePAYMENT_REFUND_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value73[] = $this->value;

            $_value71 = array();
            $_cut72 = $this->cut;

            while (true) {
                $_position70 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_REFUND();

                if (!$_success) {
                    break;
                }

                $_value71[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position70;
                $this->value = $_value71;
            }

            $this->cut = $_cut72;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value73[] = $this->value;

            $this->value = $_value73;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Section('RefundPaymentResponseSection', $open, ...$records);
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

        $_value74 = array();

        if (substr($this->string, $this->position, strlen('17')) === '17') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('17'));
            $this->position += strlen('17');
        } else {
            $_success = false;

            $this->report($this->position, '\'17\'');
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseINT5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value74[] = $this->value;

            $this->value = $_value74;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$account, &$date, &$serial, &$amount, &$nrRecs) {
                $nrRecs->setName('IncomingPaymentCount');
                return new Record('RefundPaymentResponseSectionOpening', $account, $date, $serial, $amount, $nrRecs);
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

    protected function parsePAYMENT_REFUND()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REFUND'][$_position])) {
            $_success = $this->cache['PAYMENT_REFUND'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REFUND'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REFUND'][$_position]['value'];

            return $_success;
        }

        $_value75 = array();

        if (substr($this->string, $this->position, strlen('77')) === '77') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('77'));
            $this->position += strlen('77');
        } else {
            $_success = false;

            $this->report($this->position, '\'77\'');
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $refundDate = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value75[] = $this->value;

            $this->value = $_value75;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$refundDate, &$status) {
                $status->setAttribute('message_id', 'AutogiroPaymentResponseFile.' . $status->getValue());
                $refundDate->setName('RefundDate');
                return new Record('RefundPaymentResponse', $date, $ival, $reps, $payerNr, $amount, $bg, $ref, $refundDate, $status);
            });
        }

        $this->cache['PAYMENT_REFUND'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REFUND');
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

        $_value79 = array();

        $_success = $this->parseOLD_PAYMENT_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_value77 = array();
            $_cut78 = $this->cut;

            while (true) {
                $_position76 = $this->position;

                $this->cut = false;
                $_success = $this->parseOLD_PAYMENT_RESPONSE();

                if (!$_success) {
                    break;
                }

                $_value77[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position76;
                $this->value = $_value77;
            }

            $this->cut = $_cut78;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseOLD_PAYMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $this->value = $_value79;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$recs, &$close) {
                $recs[] = $close;
                return new AutogiroFile('AutogiroPaymentResponseOldFile', $open, ...$recs);
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

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value80[] = $this->value;

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

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
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

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $this->value = $_value80;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Record('Opening', $date, $bgcNr, $bg);
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

    protected function parseOLD_PAYMENT_RESPONSE()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_PAYMENT_RESPONSE'][$_position])) {
            $_success = $this->cache['OLD_PAYMENT_RESPONSE'][$_position]['success'];
            $this->position = $this->cache['OLD_PAYMENT_RESPONSE'][$_position]['position'];
            $this->value = $this->cache['OLD_PAYMENT_RESPONSE'][$_position]['value'];

            return $_success;
        }

        $_value87 = array();

        $_position81 = $this->position;
        $_cut82 = $this->cut;

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
            $this->position = $_position81;

            if (substr($this->string, $this->position, strlen('82')) === '82') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('82'));
                $this->position += strlen('82');
            } else {
                $_success = false;

                $this->report($this->position, '\'82\'');
            }
        }

        $this->cut = $_cut82;

        if ($_success) {
            $type = $this->value;
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseA();
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

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
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

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_position83 = $this->position;
            $_cut84 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA10();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position83;
                $this->value = null;
            }

            $this->cut = $_cut84;
        }

        if ($_success) {
            $_value87[] = $this->value;

            $_position85 = $this->position;
            $_cut86 = $this->cut;

            $this->cut = false;
            $_success = $this->parseMSG1();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position85;
                $this->value = null;
            }

            $this->cut = $_cut86;

            if ($_success) {
                $status = $this->value;
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
            $this->value = call_user_func(function () use (&$type, &$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                $flag = 'Successful';

                if ($status) {
                    $status->setAttribute('message_id', 'AutogiroPaymentResponseFile.' . $status->getValue());
                    if ($status->getValue() != '0') {
                        $flag = 'Failed';
                    }
                }

                $types = [
                    '32' => 'OutgoingPaymentResponse',
                    '82' => 'IncomingPaymentResponse'
                ];

                return new Record($types[$type], new Flag($flag), $date, $ival, $reps, $payerNr, $amount, $bg, $ref, $status);
            });
        }

        $this->cache['OLD_PAYMENT_RESPONSE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_PAYMENT_RESPONSE');
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

            $_success = $this->parseDATE8();

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

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value88[] = $this->value;

            if (substr($this->string, $this->position, strlen('0000')) === '0000') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('0000'));
                $this->position += strlen('0000');
            } else {
                $_success = false;

                $this->report($this->position, '\'0000\'');
            }
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
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
            $this->value = call_user_func(function () use (&$date, &$amountOut, &$nrOut, &$nrIn, &$amountIn) {
                return new Record(
                    'Closing',
                    $date,
                    new Summary('OutgoingPaymentResponse', $amountOut),
                    new Count('OutgoingPaymentResponse', $nrOut),
                    new Count('IncomingPaymentResponse', $nrIn),
                    new Summary('IncomingPaymentResponse', $amountIn)
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

    protected function parseBGMAX_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['BGMAX_FILE'][$_position])) {
            $_success = $this->cache['BGMAX_FILE'][$_position]['success'];
            $this->position = $this->cache['BGMAX_FILE'][$_position]['position'];
            $this->value = $this->cache['BGMAX_FILE'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen('01BGMAX')) === '01BGMAX') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01BGMAX'));
            $this->position += strlen('01BGMAX');
        } else {
            $_success = false;

            $this->report($this->position, '\'01BGMAX\'');
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                throw new ContentException(['BGMAX format currently not supported']);
            });
        }

        $this->cache['BGMAX_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'BGMAX_FILE');
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

        $_value94 = array();

        $_position89 = $this->position;
        $_cut90 = $this->cut;

        $this->cut = false;
        $_success = $this->parseOLD_MANDATE_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position89;

            $_success = $this->parseMANDATE_OPENING();
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
                $_success = $this->parseMANDATE();

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
                $mands = $this->value;
            }
        }

        if ($_success) {
            $_value94[] = $this->value;

            $_success = $this->parseMANDATE_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value94[] = $this->value;

            $this->value = $_value94;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$mands, &$close) {
                $mands[] = $close;
                return new AutogiroFile('AutogiroMandateResponseFile', $open, ...$mands);
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

    protected function parseMANDATE_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_OPENING'][$_position])) {
            $_success = $this->cache['MANDATE_OPENING'][$_position]['success'];
            $this->position = $this->cache['MANDATE_OPENING'][$_position]['position'];
            $this->value = $this->cache['MANDATE_OPENING'][$_position]['value'];

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

            $_success = $this->parseDATE8();

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
            $_value95[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseS();
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
                return new Record('Opening', $date, $bgcNr, $bg);
            });
        }

        $this->cache['MANDATE_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_OPENING');
        }

        return $_success;
    }

    protected function parseOLD_MANDATE_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['OLD_MANDATE_OPENING'][$_position])) {
            $_success = $this->cache['OLD_MANDATE_OPENING'][$_position]['success'];
            $this->position = $this->cache['OLD_MANDATE_OPENING'][$_position]['position'];
            $this->value = $this->cache['OLD_MANDATE_OPENING'][$_position]['value'];

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

            $_success = $this->parseDATE8();

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

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value96[] = $this->value;

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
            $_value96[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
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
            $this->value = call_user_func(function () use (&$date, &$bg) {
                return new Record('Opening', $date, $bg);
            });
        }

        $this->cache['OLD_MANDATE_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'OLD_MANDATE_OPENING');
        }

        return $_success;
    }

    protected function parseMANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE'][$_position])) {
            $_success = $this->cache['MANDATE'][$_position]['success'];
            $this->position = $this->cache['MANDATE'][$_position]['position'];
            $this->value = $this->cache['MANDATE'][$_position]['value'];

            return $_success;
        }

        $_value101 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseACCOUNT16();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_position97 = $this->position;
            $_cut98 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS5();

            if (!$_success && !$this->cut) {
                $this->position = $_position97;

                if (substr($this->string, $this->position, strlen('00000')) === '00000') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen('00000'));
                    $this->position += strlen('00000');
                } else {
                    $_success = false;

                    $this->report($this->position, '\'00000\'');
                }
            }

            $this->cut = $_cut98;
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_position99 = $this->position;
            $_cut100 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE6();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position99;
                $this->value = null;
            }

            $this->cut = $_cut100;

            if ($_success) {
                $validDate = $this->value;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value101[] = $this->value;

            $this->value = $_value101;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$account, &$id, &$info, &$status, &$date, &$validDate) {
                // If account is empty a valid bankgiro number may be read from the payer number field
                if (!trim($account->getChild('Number')->getValue())) {
                    $account = new Container('Account', new Number($account->getLineNr(), $payerNr->getValue()));
                }

                $info->setAttribute('message_id', "73.info.{$info->getValue()}");
                $status->setAttribute('message_id', "73.status.{$status->getValue()}");

                $validDate->setName('ValidFromDate');

                return new Record('MandateResponse', $bg, $payerNr, $account, $id, $info, $status, $date, $validDate);
            });
        }

        $this->cache['MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE');
        }

        return $_success;
    }

    protected function parseMANDATE_CLOSING()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_CLOSING'][$_position])) {
            $_success = $this->cache['MANDATE_CLOSING'][$_position]['success'];
            $this->position = $this->cache['MANDATE_CLOSING'][$_position]['position'];
            $this->value = $this->cache['MANDATE_CLOSING'][$_position]['value'];

            return $_success;
        }

        $_value102 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value102[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value102[] = $this->value;

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
            $_value102[] = $this->value;

            $_success = $this->parseINT7();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value102[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value102[] = $this->value;

            $this->value = $_value102;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrRecs) {
                return new Record('Closing', $date, new Count('MandateResponse', $nrRecs));
            });
        }

        $this->cache['MANDATE_CLOSING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_CLOSING');
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

        $_value108 = array();

        $_position103 = $this->position;
        $_cut104 = $this->cut;

        $this->cut = false;
        $_success = $this->parsePAYMENT_REJECTION_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position103;

            $_success = $this->parseOLD_PAYMENT_REJECTION_OPENING();
        }

        $this->cut = $_cut104;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value108[] = $this->value;

            $_value106 = array();
            $_cut107 = $this->cut;

            while (true) {
                $_position105 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_REJECTION();

                if (!$_success) {
                    break;
                }

                $_value106[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position105;
                $this->value = $_value106;
            }

            $this->cut = $_cut107;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value108[] = $this->value;

            $_success = $this->parsePAYMENT_REJECTION_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value108[] = $this->value;

            $this->value = $_value108;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$recs, &$close) {
                $recs[] = $close;
                return new AutogiroFile('AutogiroPaymentRejectionFile', $open, ...$recs);
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

        $_value109 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value109[] = $this->value;

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
            $_value109[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value109[] = $this->value;

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
            $_value109[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value109[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value109[] = $this->value;

            $this->value = $_value109;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Record('Opening', $date, $bgcNr, $bg);
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

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value110[] = $this->value;

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
            $_value110[] = $this->value;

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
            $_value110[] = $this->value;

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
            $_value110[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value110[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value110[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value110[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value110[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value110[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value110[] = $this->value;

            $this->value = $_value110;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Record('Opening', $date, $bgcNr, $bg);
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

    protected function parsePAYMENT_REJECTION()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REJECTION'][$_position])) {
            $_success = $this->cache['PAYMENT_REJECTION'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REJECTION'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REJECTION'][$_position]['value'];

            return $_success;
        }

        $_value113 = array();

        $_position111 = $this->position;
        $_cut112 = $this->cut;

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
            $this->position = $_position111;

            if (substr($this->string, $this->position, strlen('32')) === '32') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('32'));
                $this->position += strlen('32');
            } else {
                $_success = false;

                $this->report($this->position, '\'32\'');
            }
        }

        $this->cut = $_cut112;

        if ($_success) {
            $tc = $this->value;
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value113[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value113[] = $this->value;

            $this->value = $_value113;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$tc, &$date, &$ival, &$reps, &$payerNr, &$amount, &$ref, &$comment) {
                $types = [
                    '82' => 'IncomingPaymentRejectionResponse',
                    '32' => 'OutgoingPaymentRejectionResponse',
                ];

                return new Record($types[$tc], $date, $ival, $reps, $payerNr, $amount, $ref, $comment);
            });
        }

        $this->cache['PAYMENT_REJECTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REJECTION');
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

        $_value114 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value114[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value114[] = $this->value;

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
            $_value114[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value114[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value114[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value114[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value114[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value114[] = $this->value;

            $this->value = $_value114;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrOut, &$amountOut, &$nrIn, &$amountIn) {
                return new Record(
                    'Closing',
                    $date,
                    new Count('OutgoingPaymentRejectionResponse', $nrOut),
                    new Summary('OutgoingPaymentRejectionResponse', $amountOut),
                    new Count('IncomingPaymentRejectionResponse', $nrIn),
                    new Summary('IncomingPaymentRejectionResponse', $amountIn)
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

        $_value115 = array();

        if (substr($this->string, $this->position, strlen('01AUTOGIRO              ')) === '01AUTOGIRO              ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01AUTOGIRO              '));
            $this->position += strlen('01AUTOGIRO              ');
        } else {
            $_success = false;

            $this->report($this->position, '\'01AUTOGIRO              \'');
        }

        if ($_success) {
            $_value115[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value115[] = $this->value;

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
            $_value115[] = $this->value;

            $this->value = $_value115;
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

        $_value116 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value116[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value116[] = $this->value;

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
            $_value116[] = $this->value;

            $this->value = $_value116;
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

        $_value122 = array();

        $_position117 = $this->position;
        $_cut118 = $this->cut;

        $this->cut = false;
        $_success = $this->parseOLD_AMENDMENT_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position117;

            $_success = $this->parseAMENDMENT_OPENING();
        }

        $this->cut = $_cut118;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_value120 = array();
            $_cut121 = $this->cut;

            while (true) {
                $_position119 = $this->position;

                $this->cut = false;
                $_success = $this->parseAMENDMENT();

                if (!$_success) {
                    break;
                }

                $_value120[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position119;
                $this->value = $_value120;
            }

            $this->cut = $_cut121;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = $this->parseAMENDMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value122[] = $this->value;

            $this->value = $_value122;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$recs, &$close) {
                $recs[] = $close;
                return new AutogiroFile('AutogiroAmendmentResponseFile', $open, ...$recs);
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

        $_value123 = array();

        $_success = $this->parseOPENING_INTRO();

        if ($_success) {
            $date = $this->value;
        }

        if ($_success) {
            $_value123[] = $this->value;

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
            $_value123[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value123[] = $this->value;

            $this->value = $_value123;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Record('Opening', $date, $bgcNr, $bg);
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

        $_value124 = array();

        $_success = $this->parseOLD_OPENING_INTRO();

        if ($_success) {
            $date = $this->value;
        }

        if ($_success) {
            $_value124[] = $this->value;

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
            $_value124[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value124[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value124[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value124[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value124[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value124[] = $this->value;

            $this->value = $_value124;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Record('Opening', $date, $bgcNr, $bg);
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

    protected function parseAMENDMENT()
    {
        $_position = $this->position;

        if (isset($this->cache['AMENDMENT'][$_position])) {
            $_success = $this->cache['AMENDMENT'][$_position]['success'];
            $this->position = $this->cache['AMENDMENT'][$_position]['position'];
            $this->value = $this->cache['AMENDMENT'][$_position]['value'];

            return $_success;
        }

        $_value127 = array();

        $_position125 = $this->position;
        $_cut126 = $this->cut;

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

        $this->position = $_position125;
        $this->cut = $_cut126;

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $type = $this->value;
            }
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseA8();
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseA8();
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseTXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value127[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value127[] = $this->value;

            $this->value = $_value127;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$status, &$date, &$payerNr, &$type, &$amount, &$ref, &$comment) {
                $status->setAttribute('message_id', 'AutogiroAmendmentResponseFile.TC.' . $status->getValue());

                $flag = in_array($status->getValue(), ['26', '27', '28', '29']) ? 'Amendment' : 'Revocation';

                $names = [
                    '00' => 'AmendmentResponse',
                    '82' => 'IncomingAmendmentResponse',
                    '32' => 'OutgoingAmendmentResponse',
                ];

                $namePrefix = in_array($comment->getValue(), ['12', '14', '18']) ? 'Successful' : 'Failed';

                $status->setName('Status');
                $type->setName('Type');
                $ref->setName('Reference');
                $comment->setName('Comment');

                return new Record($namePrefix.$names[$type->getValue()], new Flag($flag), $status, $date, $payerNr, $type, $amount, $ref, $comment);
            });
        }

        $this->cache['AMENDMENT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AMENDMENT');
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

        $_value128 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value128[] = $this->value;

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
            $_value128[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseA4();
        }

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value128[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value128[] = $this->value;

            $this->value = $_value128;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$amountOut, &$nrOut, &$nrIn, &$amountIn) {
                return new Record(
                    'Closing',
                    $date,
                    new Summary('SuccessfulOutgoingAmendmentResponse', $amountOut),
                    new Count('SuccessfulOutgoingAmendmentResponse', $nrOut),
                    new Count('SuccessfulIncomingAmendmentResponse', $nrIn),
                    new Summary('SuccessfulIncomingAmendmentResponse', $amountIn)
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

        $_position130 = $this->position;

        $_value129 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value129[] = $this->value;

            $_success = $this->parseA5();
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
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new Container('Account', new Number($this->lineNr, $number));
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

        $_position132 = $this->position;

        $_value131 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseA5();
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
                return new Container('Account', new Number($this->lineNr, $number));
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

        $_position134 = $this->position;

        $_value133 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value133[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value133[] = $this->value;

            $this->value = $_value133;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position134, $this->position - $_position134));
        }

        if ($_success) {
            $amount = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$amount) {
                return new Container('Amount', new Text($this->lineNr, trim($amount)));
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

        $_position136 = $this->position;

        $_value135 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseA5();
        }

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
            $amount = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$amount) {
                return new Container('Amount', new Text($this->lineNr, trim($amount)));
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
                return new Container('PayeeBankgiro', new Number($this->lineNr, $number));
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

        $_position138 = $this->position;

        $_value137 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value137[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value137[] = $this->value;

            $this->value = $_value137;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position138, $this->position - $_position138));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new Container('StateId', new Number($this->lineNr, trim($number)));
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

        $_position140 = $this->position;

        $_value139 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value139[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value139[] = $this->value;

            $this->value = $_value139;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position140, $this->position - $_position140));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new Number($this->lineNr, $nr, 'PayeeBgcNumber');
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

    protected function parseDATE6()
    {
        $_position = $this->position;

        if (isset($this->cache['DATE6'][$_position])) {
            $_success = $this->cache['DATE6'][$_position]['success'];
            $this->position = $this->cache['DATE6'][$_position]['position'];
            $this->value = $this->cache['DATE6'][$_position]['value'];

            return $_success;
        }

        $_position142 = $this->position;

        $_value141 = array();

        $_success = $this->parseA5();

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
            $date = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date) {
                return new Date(new Number($this->lineNr, trim($date)));
            });
        }

        $this->cache['DATE6'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'DATE6');
        }

        return $_success;
    }

    protected function parseDATE8()
    {
        $_position = $this->position;

        if (isset($this->cache['DATE8'][$_position])) {
            $_success = $this->cache['DATE8'][$_position]['success'];
            $this->position = $this->cache['DATE8'][$_position]['position'];
            $this->value = $this->cache['DATE8'][$_position]['value'];

            return $_success;
        }

        $_position144 = $this->position;

        $_value143 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value143[] = $this->value;

            $_success = $this->parseA2();
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
            $date = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date) {
                return new Date(new Number($this->lineNr, trim($date)));
            });
        }

        $this->cache['DATE8'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'DATE8');
        }

        return $_success;
    }

    protected function parseDATE20()
    {
        $_position = $this->position;

        if (isset($this->cache['DATE20'][$_position])) {
            $_success = $this->cache['DATE20'][$_position]['success'];
            $this->position = $this->cache['DATE20'][$_position]['position'];
            $this->value = $this->cache['DATE20'][$_position]['value'];

            return $_success;
        }

        $_position146 = $this->position;

        $_value145 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value145[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value145[] = $this->value;

            $this->value = $_value145;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position146, $this->position - $_position146));
        }

        if ($_success) {
            $date = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date) {
                return new Date(new Number($this->lineNr, trim($date)));
            });
        }

        $this->cache['DATE20'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'DATE20');
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
                return new ImmediateDate($this->lineNr);
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

    protected function parseINTERVAL()
    {
        $_position = $this->position;

        if (isset($this->cache['INTERVAL'][$_position])) {
            $_success = $this->cache['INTERVAL'][$_position]['success'];
            $this->position = $this->cache['INTERVAL'][$_position]['position'];
            $this->value = $this->cache['INTERVAL'][$_position]['value'];

            return $_success;
        }

        $_position147 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position147, $this->position - $_position147));
        }

        if ($_success) {
            $interval = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$interval) {
                return new Interval($this->lineNr, $interval);
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

        $_position148 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position148, $this->position - $_position148));
        }

        if ($_success) {
            $msg = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$msg) {
                return new Message($this->lineNr, $msg);
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

        $_position150 = $this->position;

        $_value149 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value149[] = $this->value;

            $this->value = $_value149;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position150, $this->position - $_position150));
        }

        if ($_success) {
            $msg = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$msg) {
                return new Message($this->lineNr, $msg);
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

        $_position152 = $this->position;

        $_value151 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value151[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value151[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value151[] = $this->value;

            $this->value = $_value151;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position152, $this->position - $_position152));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new Number($this->lineNr, trim($nr), 'PayerNumber');
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

        $_position154 = $this->position;

        $_value153 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value153[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value153[] = $this->value;

            $this->value = $_value153;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position154, $this->position - $_position154));
        }

        if ($_success) {
            $repetitions = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$repetitions) {
                return new Number($this->lineNr, trim($repetitions), 'Repetitions');
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

        $_position155 = $this->position;

        $_success = $this->parseA5();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position155, $this->position - $_position155));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, $integer);
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

        $_position157 = $this->position;

        $_value156 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value156[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value156[] = $this->value;

            $this->value = $_value156;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position157, $this->position - $_position157));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, $integer);
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

        $_position159 = $this->position;

        $_value158 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value158[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value158[] = $this->value;

            $this->value = $_value158;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position159, $this->position - $_position159));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, $integer);
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

        $_position161 = $this->position;

        $_value160 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value160[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value160[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value160[] = $this->value;

            $this->value = $_value160;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position161, $this->position - $_position161));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, $integer);
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

        $_position163 = $this->position;

        $_value162 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value162[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value162[] = $this->value;

            $this->value = $_value162;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position163, $this->position - $_position163));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, $integer);
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

        $_position167 = $this->position;

        $_value165 = array();
        $_cut166 = $this->cut;

        while (true) {
            $_position164 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success) {
                break;
            }

            $_value165[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position164;
            $this->value = $_value165;
        }

        $this->cut = $_cut166;

        if ($_success) {
            $this->value = strval(substr($this->string, $_position167, $this->position - $_position167));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new Text($this->lineNr, $text);
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

        $_position201 = $this->position;

        $_value200 = array();

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

        if ($_success) {
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

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
            $_value200[] = $this->value;

            $_position192 = $this->position;
            $_cut193 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position192;
                $this->value = null;
            }

            $this->cut = $_cut193;
        }

        if ($_success) {
            $_value200[] = $this->value;

            $_position194 = $this->position;
            $_cut195 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position194;
                $this->value = null;
            }

            $this->cut = $_cut195;
        }

        if ($_success) {
            $_value200[] = $this->value;

            $_position196 = $this->position;
            $_cut197 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position196;
                $this->value = null;
            }

            $this->cut = $_cut197;
        }

        if ($_success) {
            $_value200[] = $this->value;

            $_position198 = $this->position;
            $_cut199 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position198;
                $this->value = null;
            }

            $this->cut = $_cut199;
        }

        if ($_success) {
            $_value200[] = $this->value;

            $this->value = $_value200;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position201, $this->position - $_position201));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new Text($this->lineNr, $text);
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

        $_position203 = $this->position;

        $_value202 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value202[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value202[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value202[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value202[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value202[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value202[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value202[] = $this->value;

            $this->value = $_value202;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position203, $this->position - $_position203));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new Text($this->lineNr, $text);
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

        $_value206 = array();

        $_position204 = $this->position;
        $_cut205 = $this->cut;

        $this->cut = false;
        $_success = $this->parseEOL();

        if (!$_success) {
            $_success = true;
            $this->value = null;
        } else {
            $_success = false;
        }

        $this->position = $_position204;
        $this->cut = $_cut205;

        if ($_success) {
            $_value206[] = $this->value;

            if ($this->position < strlen($this->string)) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }
        }

        if ($_success) {
            $_value206[] = $this->value;

            $this->value = $_value206;
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

        $_position208 = $this->position;

        $_value207 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value207[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value207[] = $this->value;

            $this->value = $_value207;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position208, $this->position - $_position208));
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

        $_position210 = $this->position;

        $_value209 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value209[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value209[] = $this->value;

            $this->value = $_value209;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position210, $this->position - $_position210));
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

        $_position212 = $this->position;

        $_value211 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value211[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value211[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value211[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value211[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value211[] = $this->value;

            $this->value = $_value211;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position212, $this->position - $_position212));
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

        $_position214 = $this->position;

        $_value213 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value213[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value213[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value213[] = $this->value;

            $this->value = $_value213;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position214, $this->position - $_position214));
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

        $_position216 = $this->position;

        $_value215 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value215[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value215[] = $this->value;

            $this->value = $_value215;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position216, $this->position - $_position216));
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

        $_position218 = $this->position;

        $_value217 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value217[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value217[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value217[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value217[] = $this->value;

            $this->value = $_value217;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position218, $this->position - $_position218));
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

        $_position220 = $this->position;

        $_value219 = array();

        $_success = $this->parseS4();

        if ($_success) {
            $_value219[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value219[] = $this->value;

            $this->value = $_value219;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position220, $this->position - $_position220));
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

        $_position222 = $this->position;

        $_value221 = array();

        $_success = $this->parseS5();

        if ($_success) {
            $_value221[] = $this->value;

            $_success = $this->parseS5();
        }

        if ($_success) {
            $_value221[] = $this->value;

            $this->value = $_value221;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position222, $this->position - $_position222));
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

        $_position224 = $this->position;

        $_value223 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value223[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value223[] = $this->value;

            $this->value = $_value223;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position224, $this->position - $_position224));
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

        $_value230 = array();

        $_value226 = array();
        $_cut227 = $this->cut;

        while (true) {
            $_position225 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success) {
                break;
            }

            $_value226[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position225;
            $this->value = $_value226;
        }

        $this->cut = $_cut227;

        if ($_success) {
            $_value230[] = $this->value;

            $_position228 = $this->position;
            $_cut229 = $this->cut;

            $this->cut = false;
            $_success = $this->parseEOL();

            if (!$_success && !$this->cut) {
                $this->position = $_position228;

                $_success = $this->parseEOF();
            }

            $this->cut = $_cut229;
        }

        if ($_success) {
            $_value230[] = $this->value;

            $this->value = $_value230;
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

        $_value233 = array();

        $_position231 = $this->position;
        $_cut232 = $this->cut;

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
            $this->position = $_position231;
            $this->value = null;
        }

        $this->cut = $_cut232;

        if ($_success) {
            $_value233[] = $this->value;

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
            $_value233[] = $this->value;

            $this->value = $_value233;
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

        $_position234 = $this->position;
        $_cut235 = $this->cut;

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

        $this->position = $_position234;
        $this->cut = $_cut235;

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

        $_value239 = array();
        $_cut240 = $this->cut;

        while (true) {
            $_position238 = $this->position;

            $this->cut = false;
            $_position236 = $this->position;
            $_cut237 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS();

            if (!$_success && !$this->cut) {
                $this->position = $_position236;

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
                $this->position = $_position236;

                $_success = $this->parseEOL();
            }

            $this->cut = $_cut237;

            if (!$_success) {
                break;
            }

            $_value239[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position238;
            $this->value = $_value239;
        }

        $this->cut = $_cut240;

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