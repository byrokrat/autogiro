<?php

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Exception\ContentException;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Container;
use byrokrat\autogiro\Tree\Count;
use byrokrat\autogiro\Tree\Date;
use byrokrat\autogiro\Tree\Flag;
use byrokrat\autogiro\Tree\ImmediateDate;
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

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parsePAYMENT_EXTRACT_FILE();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parseMANDATE_EXTRACT_FILE();
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
        $_success = $this->parseREQ_MANDATE_SEC();

        if (!$_success && !$this->cut) {
            $this->position = $_position4;

            $_success = $this->parseREQ_PAYMENT_SEC();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position4;

            $_success = $this->parseREQ_AMENDMENT_SEC();
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
                $_success = $this->parseREQ_MANDATE_SEC();

                if (!$_success && !$this->cut) {
                    $this->position = $_position4;

                    $_success = $this->parseREQ_PAYMENT_SEC();
                }

                if (!$_success && !$this->cut) {
                    $this->position = $_position4;

                    $_success = $this->parseREQ_AMENDMENT_SEC();
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
            $secs = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$secs) {
                return new AutogiroFile('AutogiroRequestFile', ...$secs);
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

            if (substr($this->string, $this->position, strlen('AUTOGIRO    ')) === 'AUTOGIRO    ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO    '));
                $this->position += strlen('AUTOGIRO    ');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO    \'');
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

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseBG10();

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

    protected function parseREQ_MANDATE_SEC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_MANDATE_SEC'][$_position])) {
            $_success = $this->cache['REQ_MANDATE_SEC'][$_position]['success'];
            $this->position = $this->cache['REQ_MANDATE_SEC'][$_position]['position'];
            $this->value = $this->cache['REQ_MANDATE_SEC'][$_position]['value'];

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

        $this->cache['REQ_MANDATE_SEC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_MANDATE_SEC');
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

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_success = $this->parsePNUM16();

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

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value17[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value17[] = $this->value;

            $_success = $this->parseA40();
        }

        if ($_success) {
            $_value17[] = $this->value;

            $_success = $this->parseA8();
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

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parsePNUM16();

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
            $_success = $this->parseID12();

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
                return $id && trim($id->getValueFrom('Number'))
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

            $_success = $this->parseBG10();

            if ($_success) {
                $oldBg = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $oldPayerNr = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $newBg = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parsePNUM16();

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
                $oldPayerNr->setName('OldPayerNumber');
                $newPayerNr->setName('NewPayerNumber');
                $oldBg->setName('OldPayeeBankgiro');
                $newBg->setName('NewPayeeBankgiro');
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

    protected function parseREQ_PAYMENT_SEC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_PAYMENT_SEC'][$_position])) {
            $_success = $this->cache['REQ_PAYMENT_SEC'][$_position]['success'];
            $this->position = $this->cache['REQ_PAYMENT_SEC'][$_position]['position'];
            $this->value = $this->cache['REQ_PAYMENT_SEC'][$_position]['value'];

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

        $this->cache['REQ_PAYMENT_SEC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_PAYMENT_SEC');
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

            $_success = $this->parseIVAL1();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseREPS3();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parsePNUM16();

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

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseREF16();

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
                static $types = [
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

    protected function parseREQ_AMENDMENT_SEC()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_AMENDMENT_SEC'][$_position])) {
            $_success = $this->cache['REQ_AMENDMENT_SEC'][$_position]['success'];
            $this->position = $this->cache['REQ_AMENDMENT_SEC'][$_position]['position'];
            $this->value = $this->cache['REQ_AMENDMENT_SEC'][$_position]['value'];

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

        $this->cache['REQ_AMENDMENT_SEC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_AMENDMENT_SEC');
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

        $_value49 = array();

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
            $_value49[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $type = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_position39 = $this->position;
            $_cut40 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE8();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position39;
                $this->value = null;
            }

            $this->cut = $_cut40;

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_position41 = $this->position;
            $_cut42 = $this->cut;

            $this->cut = false;
            $_success = $this->parseAMOUNT12();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position41;
                $this->value = null;
            }

            $this->cut = $_cut42;

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_position43 = $this->position;
            $_cut44 = $this->cut;

            $this->cut = false;
            $_success = $this->parseMSG2();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position43;
                $this->value = null;
            }

            $this->cut = $_cut44;

            if ($_success) {
                $dir = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_position45 = $this->position;
            $_cut46 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE8();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position45;
                $this->value = null;
            }

            $this->cut = $_cut46;

            if ($_success) {
                $newDate = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_position47 = $this->position;
            $_cut48 = $this->cut;

            $this->cut = false;
            $_success = $this->parseREF16();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position47;
                $this->value = null;
            }

            $this->cut = $_cut48;

            if ($_success) {
                $ref = $this->value;
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
            $this->value = call_user_func(function () use (&$type, &$bg, &$payerNr, &$date, &$amount, &$dir, &$newDate, &$ref) {
                $type->setName('Type');

                if ($dir) {
                    $dir->setName('Direction');
                }

                if ($newDate) {
                    $newDate->setName('NewDate');
                }

                return new Record('AmendmentRequest', $type, $bg, $payerNr, $date, $amount, $dir, $newDate, $ref);
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

        $_position50 = $this->position;
        $_cut51 = $this->cut;

        $this->cut = false;
        $_success = $this->parseNEW_PAYMENT_FILE();

        if (!$_success && !$this->cut) {
            $this->position = $_position50;

            $_success = $this->parseOLD_PAYMENT_FILE();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position50;

            $_success = $this->parseBGMAX_FILE();
        }

        $this->cut = $_cut51;

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

        $_value57 = array();

        $_success = $this->parsePAYMENT_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_position52 = $this->position;
            $_cut53 = $this->cut;

            $this->cut = false;
            $_success = $this->parsePAYMENT_INCOMING_SEC();

            if (!$_success && !$this->cut) {
                $this->position = $_position52;

                $_success = $this->parsePAYMENT_OUTGOING_SEC();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position52;

                $_success = $this->parsePAYMENT_REFUND_SEC();
            }

            $this->cut = $_cut53;

            if ($_success) {
                $_value55 = array($this->value);
                $_cut56 = $this->cut;

                while (true) {
                    $_position54 = $this->position;

                    $this->cut = false;
                    $_position52 = $this->position;
                    $_cut53 = $this->cut;

                    $this->cut = false;
                    $_success = $this->parsePAYMENT_INCOMING_SEC();

                    if (!$_success && !$this->cut) {
                        $this->position = $_position52;

                        $_success = $this->parsePAYMENT_OUTGOING_SEC();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position52;

                        $_success = $this->parsePAYMENT_REFUND_SEC();
                    }

                    $this->cut = $_cut53;

                    if (!$_success) {
                        break;
                    }

                    $_value55[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position54;
                    $this->value = $_value55;
                }

                $this->cut = $_cut56;
            }

            if ($_success) {
                $secs = $this->value;
            }
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parsePAYMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value57[] = $this->value;

            $this->value = $_value57;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$secs, &$close) {
                $secs[] = $close;
                return new AutogiroFile('AutogiroPaymentResponseFile', $open, ...$secs);
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

        $_value58 = array();

        if (substr($this->string, $this->position, strlen('01AUTOGIRO    ')) === '01AUTOGIRO    ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01AUTOGIRO    '));
            $this->position += strlen('01AUTOGIRO    ');
        } else {
            $_success = false;

            $this->report($this->position, '\'01AUTOGIRO    \'');
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseDATE20();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

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
            $_value58[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
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

        $_value59 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
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
            $_value59[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrInSecs = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrInRecs = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOutSecs = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrOutRecs = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrRefSecs = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseINT12();

            if ($_success) {
                $nrRefRecs = $this->value;
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

    protected function parsePAYMENT_INCOMING_SEC()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_INCOMING_SEC'][$_position])) {
            $_success = $this->cache['PAYMENT_INCOMING_SEC'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_INCOMING_SEC'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_INCOMING_SEC'][$_position]['value'];

            return $_success;
        }

        $_value63 = array();

        $_success = $this->parsePAYMENT_INCOMING_OPENING();

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
                $_success = $this->parsePAYMENT_INCOMING();

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
                return new Section('IncomingPaymentResponseSection', $open, ...$records);
            });
        }

        $this->cache['PAYMENT_INCOMING_SEC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_INCOMING_SEC');
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

        $_value64 = array();

        if (substr($this->string, $this->position, strlen('15')) === '15') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('15'));
            $this->position += strlen('15');
        } else {
            $_success = false;

            $this->report($this->position, '\'15\'');
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

            $_success = $this->parseSERIAL5();

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

        $_value69 = array();

        if (substr($this->string, $this->position, strlen('82')) === '82') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('82'));
            $this->position += strlen('82');
        } else {
            $_success = false;

            $this->report($this->position, '\'82\'');
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

            $_success = $this->parseIVAL1();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseREPS3();

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

            $_success = $this->parsePNUM16();

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

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseREF16();

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
                if (!$status) {
                    $status = new Message('', new Number($date->getLineNr(), '0'));
                }

                $status->setName('Status');
                $flag = !$status->getValueFrom('Number') ? 'Successful' : 'Failed';

                return new Record($flag.'IncomingPaymentResponse', new Flag($flag.'Flag'), $date, $ival, $reps, $payerNr, $amount, $bg, $ref, $status);
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

    protected function parsePAYMENT_OUTGOING_SEC()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_OUTGOING_SEC'][$_position])) {
            $_success = $this->cache['PAYMENT_OUTGOING_SEC'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_OUTGOING_SEC'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_OUTGOING_SEC'][$_position]['value'];

            return $_success;
        }

        $_value73 = array();

        $_success = $this->parsePAYMENT_OUTGOING_OPENING();

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
                $_success = $this->parsePAYMENT_OUTGOING();

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
                return new Section('OutgoingPaymentResponseSection', $open, ...$records);
            });
        }

        $this->cache['PAYMENT_OUTGOING_SEC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_OUTGOING_SEC');
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

        $_value74 = array();

        if (substr($this->string, $this->position, strlen('16')) === '16') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('16'));
            $this->position += strlen('16');
        } else {
            $_success = false;

            $this->report($this->position, '\'16\'');
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

            $_success = $this->parseSERIAL5();

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

        $_value79 = array();

        if (substr($this->string, $this->position, strlen('32')) === '32') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('32'));
            $this->position += strlen('32');
        } else {
            $_success = false;

            $this->report($this->position, '\'32\'');
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseIVAL1();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseREPS3();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseREF16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_position75 = $this->position;
            $_cut76 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA10();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position75;
                $this->value = null;
            }

            $this->cut = $_cut76;
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_position77 = $this->position;
            $_cut78 = $this->cut;

            $this->cut = false;
            $_success = $this->parseMSG1();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position77;
                $this->value = null;
            }

            $this->cut = $_cut78;

            if ($_success) {
                $status = $this->value;
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
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                if (!$status) {
                    $status = new Message('', new Number($date->getLineNr(), '0'));
                }

                $status->setName('Status');
                $flag = $status->getValueFrom('Number') == '0' ? 'Successful' : 'Failed';

                return new Record($flag.'OutgoingPaymentResponse', new Flag($flag.'Flag'), $date, $ival, $reps, $payerNr, $amount, $bg, $ref, $status);
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

    protected function parsePAYMENT_REFUND_SEC()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_REFUND_SEC'][$_position])) {
            $_success = $this->cache['PAYMENT_REFUND_SEC'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_REFUND_SEC'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_REFUND_SEC'][$_position]['value'];

            return $_success;
        }

        $_value83 = array();

        $_success = $this->parsePAYMENT_REFUND_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value83[] = $this->value;

            $_value81 = array();
            $_cut82 = $this->cut;

            while (true) {
                $_position80 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_REFUND();

                if (!$_success) {
                    break;
                }

                $_value81[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position80;
                $this->value = $_value81;
            }

            $this->cut = $_cut82;

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value83[] = $this->value;

            $this->value = $_value83;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return new Section('RefundPaymentResponseSection', $open, ...$records);
            });
        }

        $this->cache['PAYMENT_REFUND_SEC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_REFUND_SEC');
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

        $_value84 = array();

        if (substr($this->string, $this->position, strlen('17')) === '17') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('17'));
            $this->position += strlen('17');
        } else {
            $_success = false;

            $this->report($this->position, '\'17\'');
        }

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseACCOUNT35();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseSERIAL5();

            if ($_success) {
                $serial = $this->value;
            }
        }

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseAMOUNT18();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseINT8();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value84[] = $this->value;

            $this->value = $_value84;
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

        $_value85 = array();

        if (substr($this->string, $this->position, strlen('77')) === '77') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('77'));
            $this->position += strlen('77');
        } else {
            $_success = false;

            $this->report($this->position, '\'77\'');
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseIVAL1();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseREPS3();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseREF16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $refundDate = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value85[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value85[] = $this->value;

            $this->value = $_value85;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$refundDate, &$status) {
                $refundDate->setName('RefundDate');
                $status->setName('Status');
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

        $_value89 = array();

        $_success = $this->parseOLD_PAYMENT_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value89[] = $this->value;

            $_value87 = array();
            $_cut88 = $this->cut;

            while (true) {
                $_position86 = $this->position;

                $this->cut = false;
                $_success = $this->parseOLD_PAYMENT_RESPONSE();

                if (!$_success) {
                    break;
                }

                $_value87[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position86;
                $this->value = $_value87;
            }

            $this->cut = $_cut88;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value89[] = $this->value;

            $_success = $this->parseOLD_PAYMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value89[] = $this->value;

            $this->value = $_value89;
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

        $_value90 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value90[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value90[] = $this->value;

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
            $_value90[] = $this->value;

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
            $_value90[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value90[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value90[] = $this->value;

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value90[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value90[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value90[] = $this->value;

            $this->value = $_value90;
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

        $_value97 = array();

        $_position91 = $this->position;
        $_cut92 = $this->cut;

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
            $this->position = $_position91;

            if (substr($this->string, $this->position, strlen('82')) === '82') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('82'));
                $this->position += strlen('82');
            } else {
                $_success = false;

                $this->report($this->position, '\'82\'');
            }
        }

        $this->cut = $_cut92;

        if ($_success) {
            $type = $this->value;
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseIVAL1();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseREPS3();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseREF16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_position93 = $this->position;
            $_cut94 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA10();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position93;
                $this->value = null;
            }

            $this->cut = $_cut94;
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_position95 = $this->position;
            $_cut96 = $this->cut;

            $this->cut = false;
            $_success = $this->parseMSG1();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position95;
                $this->value = null;
            }

            $this->cut = $_cut96;

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value97[] = $this->value;

            $this->value = $_value97;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$type, &$date, &$ival, &$reps, &$payerNr, &$amount, &$bg, &$ref, &$status) {
                if (!$status) {
                    $status = new Message('', new Number($date->getLineNr(), '0'));
                }

                $status->setName('Status');
                $flag = $status->getValueFrom('Number') == '0' ? 'SuccessfulFlag' : 'FailedFlag';

                static $types = [
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

        $_value98 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value98[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value98[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900    ')) === '9900    ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900    '));
                $this->position += strlen('9900    ');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900    \'');
            }
        }

        if ($_success) {
            $_value98[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value98[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value98[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value98[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value98[] = $this->value;

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
            $_value98[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value98[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value98[] = $this->value;

            $this->value = $_value98;
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

        $_value104 = array();

        $_position99 = $this->position;
        $_cut100 = $this->cut;

        $this->cut = false;
        $_success = $this->parseOLD_MANDATE_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position99;

            $_success = $this->parseMANDATE_OPENING();
        }

        $this->cut = $_cut100;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_value102 = array();
            $_cut103 = $this->cut;

            while (true) {
                $_position101 = $this->position;

                $this->cut = false;
                $_success = $this->parseMANDATE();

                if (!$_success) {
                    break;
                }

                $_value102[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position101;
                $this->value = $_value102;
            }

            $this->cut = $_cut103;

            if ($_success) {
                $mands = $this->value;
            }
        }

        if ($_success) {
            $_value104[] = $this->value;

            $_success = $this->parseMANDATE_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value104[] = $this->value;

            $this->value = $_value104;
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

        $_value105 = array();

        if (substr($this->string, $this->position, strlen('01AUTOGIRO    ')) === '01AUTOGIRO    ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01AUTOGIRO    '));
            $this->position += strlen('01AUTOGIRO    ');
        } else {
            $_success = false;

            $this->report($this->position, '\'01AUTOGIRO    \'');
        }

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value105[] = $this->value;

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
            $_value105[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value105[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value105[] = $this->value;

            $this->value = $_value105;
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

        $_value106 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value106[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value106[] = $this->value;

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
            $_value106[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value106[] = $this->value;

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
            $_value106[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value106[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value106[] = $this->value;

            $this->value = $_value106;
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

        $_value111 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseACCOUNT16();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseID12();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_position107 = $this->position;
            $_cut108 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS5();

            if (!$_success && !$this->cut) {
                $this->position = $_position107;

                if (substr($this->string, $this->position, strlen('00000')) === '00000') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen('00000'));
                    $this->position += strlen('00000');
                } else {
                    $_success = false;

                    $this->report($this->position, '\'00000\'');
                }
            }

            $this->cut = $_cut108;
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_position109 = $this->position;
            $_cut110 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE6();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position109;
                $this->value = null;
            }

            $this->cut = $_cut110;

            if ($_success) {
                $validDate = $this->value;
            }
        }

        if ($_success) {
            $_value111[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value111[] = $this->value;

            $this->value = $_value111;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$account, &$id, &$info, &$status, &$date, &$validDate) {
                // If account is empty a valid bankgiro number may be read from the payer number field
                if (!trim($account->getValueFrom('Number'))) {
                    $account = new Container('Account', new Number($account->getLineNr(), $payerNr->getValue()));
                }

                $info->setName('Info');
                $status->setName('Status');

                static $status2flag = [
                    '32' => 'CreatedFlag',
                    '02' => 'DeletedFlag',
                    '07' => 'DeletedFlag',
                    '33' => 'DeletedFlag',
                    '98' => 'DeletedFlag',
                    '01' => 'DeletedFlag',
                    '06' => 'DeletedFlag',
                ];

                $flag = $status2flag[$status->getValueFrom('Number')] ?? 'ErrorFlag';

                if ($validDate) {
                    $validDate->setName('ValidFromDate');
                }

                return new Record('MandateResponse', new Flag($flag), $bg, $payerNr, $account, $id, $info, $status, $date, $validDate);
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

        $_value112 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value112[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value112[] = $this->value;

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
            $_value112[] = $this->value;

            $_success = $this->parseINT7();

            if ($_success) {
                $nrRecs = $this->value;
            }
        }

        if ($_success) {
            $_value112[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value112[] = $this->value;

            $this->value = $_value112;
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

        $_value118 = array();

        $_position113 = $this->position;
        $_cut114 = $this->cut;

        $this->cut = false;
        $_success = $this->parsePAYMENT_REJECTION_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position113;

            $_success = $this->parseOLD_PAYMENT_REJECTION_OPENING();
        }

        $this->cut = $_cut114;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_value116 = array();
            $_cut117 = $this->cut;

            while (true) {
                $_position115 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_REJECTION();

                if (!$_success) {
                    break;
                }

                $_value116[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position115;
                $this->value = $_value116;
            }

            $this->cut = $_cut117;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_success = $this->parsePAYMENT_REJECTION_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value118[] = $this->value;

            $this->value = $_value118;
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

        $_value119 = array();

        if (substr($this->string, $this->position, strlen('01AUTOGIRO    ')) === '01AUTOGIRO    ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01AUTOGIRO    '));
            $this->position += strlen('01AUTOGIRO    ');
        } else {
            $_success = false;

            $this->report($this->position, '\'01AUTOGIRO    \'');
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value119[] = $this->value;

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
            $_value119[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value119[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value119[] = $this->value;

            $this->value = $_value119;
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

        $_value120 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value120[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value120[] = $this->value;

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
            $_value120[] = $this->value;

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
            $_value120[] = $this->value;

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
            $_value120[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value120[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value120[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value120[] = $this->value;

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value120[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value120[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value120[] = $this->value;

            $this->value = $_value120;
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

        $_value123 = array();

        $_position121 = $this->position;
        $_cut122 = $this->cut;

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
            $this->position = $_position121;

            if (substr($this->string, $this->position, strlen('32')) === '32') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('32'));
                $this->position += strlen('32');
            } else {
                $_success = false;

                $this->report($this->position, '\'32\'');
            }
        }

        $this->cut = $_cut122;

        if ($_success) {
            $tc = $this->value;
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseIVAL1();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseREPS3();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseREF16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value123[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $comment = $this->value;
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
            $this->value = call_user_func(function () use (&$tc, &$date, &$ival, &$reps, &$payerNr, &$amount, &$ref, &$comment) {
                static $types = [
                    '82' => 'IncomingPaymentRejectionResponse',
                    '32' => 'OutgoingPaymentRejectionResponse',
                ];

                $comment->setName('Comment');

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

        $_value124 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value124[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value124[] = $this->value;

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
            $_value124[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value124[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value124[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value124[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
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

    protected function parseAMENDMENT_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['AMENDMENT_FILE'][$_position])) {
            $_success = $this->cache['AMENDMENT_FILE'][$_position]['success'];
            $this->position = $this->cache['AMENDMENT_FILE'][$_position]['position'];
            $this->value = $this->cache['AMENDMENT_FILE'][$_position]['value'];

            return $_success;
        }

        $_value130 = array();

        $_position125 = $this->position;
        $_cut126 = $this->cut;

        $this->cut = false;
        $_success = $this->parseOLD_AMENDMENT_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position125;

            $_success = $this->parseAMENDMENT_OPENING();
        }

        $this->cut = $_cut126;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value130[] = $this->value;

            $_value128 = array();
            $_cut129 = $this->cut;

            while (true) {
                $_position127 = $this->position;

                $this->cut = false;
                $_success = $this->parseAMENDMENT();

                if (!$_success) {
                    break;
                }

                $_value128[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position127;
                $this->value = $_value128;
            }

            $this->cut = $_cut129;

            if ($_success) {
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value130[] = $this->value;

            $_success = $this->parseAMENDMENT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value130[] = $this->value;

            $this->value = $_value130;
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

        $_value131 = array();

        if (substr($this->string, $this->position, strlen('01AUTOGIRO    ')) === '01AUTOGIRO    ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01AUTOGIRO    '));
            $this->position += strlen('01AUTOGIRO    ');
        } else {
            $_success = false;

            $this->report($this->position, '\'01AUTOGIRO    \'');
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value131[] = $this->value;

            if (substr($this->string, $this->position, strlen('  MAKULERING/ÄNDRING  ')) === '  MAKULERING/ÄNDRING  ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('  MAKULERING/ÄNDRING  '));
                $this->position += strlen('  MAKULERING/ÄNDRING  ');
            } else {
                $_success = false;

                $this->report($this->position, '\'  MAKULERING/ÄNDRING  \'');
            }
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value131[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value131[] = $this->value;

            $this->value = $_value131;
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

        $_value132 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value132[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value132[] = $this->value;

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
            $_value132[] = $this->value;

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
            $_value132[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value132[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value132[] = $this->value;

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value132[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value132[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value132[] = $this->value;

            $this->value = $_value132;
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

        $_value135 = array();

        $_position133 = $this->position;
        $_cut134 = $this->cut;

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

        $this->position = $_position133;
        $this->cut = $_cut134;

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $type = $this->value;
            }
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $dir = $this->value;
            }
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseA8();
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseA8();
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseREF16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseMSG2();

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value135[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value135[] = $this->value;

            $this->value = $_value135;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$type, &$date, &$payerNr, &$dir, &$amount, &$ref, &$comment) {
                $type->setName('Type');
                $dir->setName('Direction');
                $ref->setName('Reference');
                $comment->setName('Comment');

                static $dirToName = [
                    '82' => 'IncomingAmendmentResponse',
                    '32' => 'OutgoingAmendmentResponse',
                ];

                static $successComments = ['12', '14', '18'];

                static $amendmentTypes = ['26', '27', '28', '29'];

                $name = (in_array($comment->getValueFrom('Number'), $successComments) ? 'Successful' : 'Failed')
                    . ($dirToName[$dir->getValueFrom('Number')] ?? 'AmendmentResponse');

                $flag = in_array($type->getValueFrom('Number'), $amendmentTypes) ? 'AmendmentFlag' : 'RevocationFlag';

                return new Record($name, new Flag($flag), $type, $date, $payerNr, $dir, $amount, $ref, $comment);
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

        $_value136 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value136[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value136[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900    ')) === '9900    ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900    '));
                $this->position += strlen('9900    ');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900    \'');
            }
        }

        if ($_success) {
            $_value136[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value136[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value136[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value136[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value136[] = $this->value;

            $_success = $this->parseA4();
        }

        if ($_success) {
            $_value136[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value136[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value136[] = $this->value;

            $this->value = $_value136;
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

    protected function parsePAYMENT_EXTRACT_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_EXTRACT_FILE'][$_position])) {
            $_success = $this->cache['PAYMENT_EXTRACT_FILE'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_EXTRACT_FILE'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_EXTRACT_FILE'][$_position]['value'];

            return $_success;
        }

        $_value140 = array();

        $_success = $this->parsePAYMENT_EXTRACT_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value140[] = $this->value;

            $_value138 = array();
            $_cut139 = $this->cut;

            while (true) {
                $_position137 = $this->position;

                $this->cut = false;
                $_success = $this->parsePAYMENT_EXTRACT();

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
                $recs = $this->value;
            }
        }

        if ($_success) {
            $_value140[] = $this->value;

            $_success = $this->parsePAYMENT_EXTRACT_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value140[] = $this->value;

            $this->value = $_value140;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$recs, &$close) {
                $recs[] = $close;
                return new AutogiroFile('AutogiroPaymentExtractFile', $open, ...$recs);
            });
        }

        $this->cache['PAYMENT_EXTRACT_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_EXTRACT_FILE');
        }

        return $_success;
    }

    protected function parsePAYMENT_EXTRACT_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_EXTRACT_OPENING'][$_position])) {
            $_success = $this->cache['PAYMENT_EXTRACT_OPENING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_EXTRACT_OPENING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_EXTRACT_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value141 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value141[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value141[] = $this->value;

            if (substr($this->string, $this->position, strlen('AUTOGIRO9900BEVAKNINGSREG       ')) === 'AUTOGIRO9900BEVAKNINGSREG       ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AUTOGIRO9900BEVAKNINGSREG       '));
                $this->position += strlen('AUTOGIRO9900BEVAKNINGSREG       ');
            } else {
                $_success = false;

                $this->report($this->position, '\'AUTOGIRO9900BEVAKNINGSREG       \'');
            }
        }

        if ($_success) {
            $_value141[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value141[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value141[] = $this->value;

            $_success = $this->parseBGC6();

            if ($_success) {
                $bgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value141[] = $this->value;

            $_success = $this->parseBG10();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value141[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value141[] = $this->value;

            $this->value = $_value141;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bgcNr, &$bg) {
                return new Record('Opening', $date, $bgcNr, $bg);
            });
        }

        $this->cache['PAYMENT_EXTRACT_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_EXTRACT_OPENING');
        }

        return $_success;
    }

    protected function parsePAYMENT_EXTRACT()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_EXTRACT'][$_position])) {
            $_success = $this->cache['PAYMENT_EXTRACT'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_EXTRACT'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_EXTRACT'][$_position]['value'];

            return $_success;
        }

        $_value148 = array();

        $_position142 = $this->position;
        $_cut143 = $this->cut;

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
            $this->position = $_position142;

            if (substr($this->string, $this->position, strlen('32')) === '32') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('32'));
                $this->position += strlen('32');
            } else {
                $_success = false;

                $this->report($this->position, '\'32\'');
            }
        }

        $this->cut = $_cut143;

        if ($_success) {
            $type = $this->value;
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parseIVAL1();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parseREPS3();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_position144 = $this->position;
            $_cut145 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA10();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position144;
                $this->value = null;
            }

            $this->cut = $_cut145;
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_position146 = $this->position;
            $_cut147 = $this->cut;

            $this->cut = false;
            $_success = $this->parseREF16();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position146;
                $this->value = null;
            }

            $this->cut = $_cut147;

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value148[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value148[] = $this->value;

            $this->value = $_value148;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$type, &$date, &$ival, &$reps, &$payerNr, &$amount, &$ref) {
                static $types = [
                    '82' => 'IncomingPayment',
                    '32' => 'OutgoingPayment',
                ];

                return new Record($types[$type], $date, $ival, $reps, $payerNr, $amount, $ref);
            });
        }

        $this->cache['PAYMENT_EXTRACT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_EXTRACT');
        }

        return $_success;
    }

    protected function parsePAYMENT_EXTRACT_CLOSING()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYMENT_EXTRACT_CLOSING'][$_position])) {
            $_success = $this->cache['PAYMENT_EXTRACT_CLOSING'][$_position]['success'];
            $this->position = $this->cache['PAYMENT_EXTRACT_CLOSING'][$_position]['position'];
            $this->value = $this->cache['PAYMENT_EXTRACT_CLOSING'][$_position]['value'];

            return $_success;
        }

        $_value149 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value149[] = $this->value;

            if (substr($this->string, $this->position, strlen('9900    ')) === '9900    ') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('9900    '));
                $this->position += strlen('9900    ');
            } else {
                $_success = false;

                $this->report($this->position, '\'9900    \'');
            }
        }

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountOut = $this->value;
            }
        }

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrOut = $this->value;
            }
        }

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseINT6();

            if ($_success) {
                $nrIn = $this->value;
            }
        }

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseA4();
        }

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseAMOUNT12();

            if ($_success) {
                $amountIn = $this->value;
            }
        }

        if ($_success) {
            $_value149[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value149[] = $this->value;

            $this->value = $_value149;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$amountOut, &$nrOut, &$nrIn, &$amountIn) {
                return new Record(
                    'Closing',
                    $date,
                    new Summary('OutgoingPayment', $amountOut),
                    new Count('OutgoingPayment', $nrOut),
                    new Count('IncomingPayment', $nrIn),
                    new Summary('IncomingPayment', $amountIn)
                );
            });
        }

        $this->cache['PAYMENT_EXTRACT_CLOSING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYMENT_EXTRACT_CLOSING');
        }

        return $_success;
    }

    protected function parseMANDATE_EXTRACT_FILE()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_EXTRACT_FILE'][$_position])) {
            $_success = $this->cache['MANDATE_EXTRACT_FILE'][$_position]['success'];
            $this->position = $this->cache['MANDATE_EXTRACT_FILE'][$_position]['position'];
            $this->value = $this->cache['MANDATE_EXTRACT_FILE'][$_position]['value'];

            return $_success;
        }

        $_position150 = $this->position;
        $_cut151 = $this->cut;

        $this->cut = false;
        $_success = $this->parseMANDATE_EXTRACT_FILE_NEW();

        if (!$_success && !$this->cut) {
            $this->position = $_position150;

            $_success = $this->parseMANDATE_EXTRACT_FILE_OLD();
        }

        $this->cut = $_cut151;

        $this->cache['MANDATE_EXTRACT_FILE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_EXTRACT_FILE');
        }

        return $_success;
    }

    protected function parseMANDATE_EXTRACT_FILE_NEW()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_EXTRACT_FILE_NEW'][$_position])) {
            $_success = $this->cache['MANDATE_EXTRACT_FILE_NEW'][$_position]['success'];
            $this->position = $this->cache['MANDATE_EXTRACT_FILE_NEW'][$_position]['position'];
            $this->value = $this->cache['MANDATE_EXTRACT_FILE_NEW'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseMANDATE_EXTRACT();

        if ($_success) {
            $_value153 = array($this->value);
            $_cut154 = $this->cut;

            while (true) {
                $_position152 = $this->position;

                $this->cut = false;
                $_success = $this->parseMANDATE_EXTRACT();

                if (!$_success) {
                    break;
                }

                $_value153[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position152;
                $this->value = $_value153;
            }

            $this->cut = $_cut154;
        }

        if ($_success) {
            $recs = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$recs) {
                return new AutogiroFile('AutogiroMandateExtractFile', ...$recs);
            });
        }

        $this->cache['MANDATE_EXTRACT_FILE_NEW'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_EXTRACT_FILE_NEW');
        }

        return $_success;
    }

    protected function parseMANDATE_EXTRACT_FILE_OLD()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_EXTRACT_FILE_OLD'][$_position])) {
            $_success = $this->cache['MANDATE_EXTRACT_FILE_OLD'][$_position]['success'];
            $this->position = $this->cache['MANDATE_EXTRACT_FILE_OLD'][$_position]['position'];
            $this->value = $this->cache['MANDATE_EXTRACT_FILE_OLD'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseMANDATE_EXTRACT_OLD();

        if ($_success) {
            $_value156 = array($this->value);
            $_cut157 = $this->cut;

            while (true) {
                $_position155 = $this->position;

                $this->cut = false;
                $_success = $this->parseMANDATE_EXTRACT_OLD();

                if (!$_success) {
                    break;
                }

                $_value156[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position155;
                $this->value = $_value156;
            }

            $this->cut = $_cut157;
        }

        if ($_success) {
            $recs = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$recs) {
                return new AutogiroFile('AutogiroMandateExtractFile', ...$recs);
            });
        }

        $this->cache['MANDATE_EXTRACT_FILE_OLD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_EXTRACT_FILE_OLD');
        }

        return $_success;
    }

    protected function parseMANDATE_EXTRACT()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_EXTRACT'][$_position])) {
            $_success = $this->cache['MANDATE_EXTRACT'][$_position]['success'];
            $this->position = $this->cache['MANDATE_EXTRACT'][$_position]['position'];
            $this->value = $this->cache['MANDATE_EXTRACT'][$_position]['value'];

            return $_success;
        }

        $_value166 = array();

        $_success = $this->parseBG10();

        if ($_success) {
            $bg = $this->value;
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_success = $this->parseID12();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_success = $this->parseMSG1();

            if ($_success) {
                $type = $this->value;
            }
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_success = $this->parseINT2();

            if ($_success) {
                $active = $this->value;
            }
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $created = $this->value;
            }
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $updated = $this->value;
            }
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_position158 = $this->position;
            $_cut159 = $this->cut;

            $this->cut = false;
            if (substr($this->string, $this->position, strlen('0')) === '0') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('0'));
                $this->position += strlen('0');
            } else {
                $_success = false;

                $this->report($this->position, '\'0\'');
            }

            if (!$_success) {
                $_success = true;
                $this->value = null;
            } else {
                $_success = false;
            }

            $this->position = $_position158;
            $this->cut = $_cut159;
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_success = $this->parseMSG1();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_position160 = $this->position;
            $_cut161 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position160;
                $this->value = null;
            }

            $this->cut = $_cut161;
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_position162 = $this->position;
            $_cut163 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS5();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position162;
                $this->value = null;
            }

            $this->cut = $_cut163;
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_position164 = $this->position;
            $_cut165 = $this->cut;

            $this->cut = false;
            $_success = $this->parseACCOUNT16();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position164;
                $this->value = null;
            }

            $this->cut = $_cut165;

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value166[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value166[] = $this->value;

            $this->value = $_value166;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$id, &$payerNr, &$type, &$active, &$created, &$updated, &$status, &$account) {
                $type->setName('Type');
                $active->setName('ActiveYear');
                $created->setName('Created');
                $updated->setName('Updated');
                $status->setName('Status');
                return new Record('Mandate', $bg, $id, $payerNr, $type, $active, $created, $updated, $status, $account);
            });
        }

        $this->cache['MANDATE_EXTRACT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_EXTRACT');
        }

        return $_success;
    }

    protected function parseMANDATE_EXTRACT_OLD()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_EXTRACT_OLD'][$_position])) {
            $_success = $this->cache['MANDATE_EXTRACT_OLD'][$_position]['success'];
            $this->position = $this->cache['MANDATE_EXTRACT_OLD'][$_position]['position'];
            $this->value = $this->cache['MANDATE_EXTRACT_OLD'][$_position]['value'];

            return $_success;
        }

        $_value171 = array();

        $_success = $this->parseBG10();

        if ($_success) {
            $bg = $this->value;
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_success = $this->parseID12();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_success = $this->parsePNUM16();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_success = $this->parseMSG1();

            if ($_success) {
                $type = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_success = $this->parseINT1();

            if ($_success) {
                $active = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $created = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_success = $this->parseDATE8();

            if ($_success) {
                $updated = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_success = $this->parseMSG1();

            if ($_success) {
                $status = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            if (substr($this->string, $this->position, strlen('0')) === '0') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('0'));
                $this->position += strlen('0');
            } else {
                $_success = false;

                $this->report($this->position, '\'0\'');
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_position167 = $this->position;
            $_cut168 = $this->cut;

            $this->cut = false;
            $_success = $this->parseINT5();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position167;
                $this->value = null;
            }

            $this->cut = $_cut168;

            if ($_success) {
                $maxAmount = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_position169 = $this->position;
            $_cut170 = $this->cut;

            $this->cut = false;
            $_success = $this->parseACCOUNT16();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position169;
                $this->value = null;
            }

            $this->cut = $_cut170;

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value171[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value171[] = $this->value;

            $this->value = $_value171;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$id, &$payerNr, &$type, &$active, &$created, &$updated, &$status, &$maxAmount, &$account) {
                $type->setName('Type');
                $active->setName('ActiveYear');
                $created->setName('Created');
                $updated->setName('Updated');
                $status->setName('Status');

                if ($maxAmount) {
                    $maxAmount->setName('MaxAmount');
                }

                return new Record('Mandate', $bg, $id, $payerNr, $type, $active, $created, $updated, $status, $maxAmount, $account);
            });
        }

        $this->cache['MANDATE_EXTRACT_OLD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_EXTRACT_OLD');
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

        $_position173 = $this->position;

        $_value172 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value172[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value172[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value172[] = $this->value;

            $this->value = $_value172;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position173, $this->position - $_position173));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new Container('Account', new Number($this->lineNr, trim($number)));
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

        $_position175 = $this->position;

        $_value174 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value174[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value174[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value174[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value174[] = $this->value;

            $this->value = $_value174;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position175, $this->position - $_position175));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new Container('Account', new Number($this->lineNr, trim($number)));
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

        $_position177 = $this->position;

        $_value176 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value176[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value176[] = $this->value;

            $this->value = $_value176;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position177, $this->position - $_position177));
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

        $_position179 = $this->position;

        $_value178 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value178[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value178[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value178[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value178[] = $this->value;

            $this->value = $_value178;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position179, $this->position - $_position179));
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

    protected function parseBG10()
    {
        $_position = $this->position;

        if (isset($this->cache['BG10'][$_position])) {
            $_success = $this->cache['BG10'][$_position]['success'];
            $this->position = $this->cache['BG10'][$_position]['position'];
            $this->value = $this->cache['BG10'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseA10();

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new Container('PayeeBankgiro', new Number($this->lineNr, trim($number)));
            });
        }

        $this->cache['BG10'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'BG10');
        }

        return $_success;
    }

    protected function parseID12()
    {
        $_position = $this->position;

        if (isset($this->cache['ID12'][$_position])) {
            $_success = $this->cache['ID12'][$_position]['success'];
            $this->position = $this->cache['ID12'][$_position]['position'];
            $this->value = $this->cache['ID12'][$_position]['value'];

            return $_success;
        }

        $_position181 = $this->position;

        $_value180 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value180[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value180[] = $this->value;

            $this->value = $_value180;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position181, $this->position - $_position181));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new Container('StateId', new Number($this->lineNr, trim($number)));
            });
        }

        $this->cache['ID12'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'ID12');
        }

        return $_success;
    }

    protected function parseBGC6()
    {
        $_position = $this->position;

        if (isset($this->cache['BGC6'][$_position])) {
            $_success = $this->cache['BGC6'][$_position]['success'];
            $this->position = $this->cache['BGC6'][$_position]['position'];
            $this->value = $this->cache['BGC6'][$_position]['value'];

            return $_success;
        }

        $_position183 = $this->position;

        $_value182 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value182[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value182[] = $this->value;

            $this->value = $_value182;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position183, $this->position - $_position183));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new Number($this->lineNr, trim($nr), 'PayeeBgcNumber');
            });
        }

        $this->cache['BGC6'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'BGC6');
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

        $_position185 = $this->position;

        $_value184 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value184[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value184[] = $this->value;

            $this->value = $_value184;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position185, $this->position - $_position185));
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

        $_position187 = $this->position;

        $_value186 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value186[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value186[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value186[] = $this->value;

            $this->value = $_value186;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position187, $this->position - $_position187));
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

        $_position189 = $this->position;

        $_value188 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value188[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value188[] = $this->value;

            $this->value = $_value188;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position189, $this->position - $_position189));
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

    protected function parseIVAL1()
    {
        $_position = $this->position;

        if (isset($this->cache['IVAL1'][$_position])) {
            $_success = $this->cache['IVAL1'][$_position]['success'];
            $this->position = $this->cache['IVAL1'][$_position]['position'];
            $this->value = $this->cache['IVAL1'][$_position]['value'];

            return $_success;
        }

        $_position190 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position190, $this->position - $_position190));
        }

        if ($_success) {
            $interval = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$interval) {
                return new Message('Interval', new Number($this->lineNr, trim($interval)));
            });
        }

        $this->cache['IVAL1'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'IVAL1');
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

        $_position191 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position191, $this->position - $_position191));
        }

        if ($_success) {
            $msg = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$msg) {
                return new Message('', new Number($this->lineNr, trim($msg)));
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

        $_position193 = $this->position;

        $_value192 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value192[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value192[] = $this->value;

            $this->value = $_value192;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position193, $this->position - $_position193));
        }

        if ($_success) {
            $msg = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$msg) {
                return new Message('', new Number($this->lineNr, trim($msg)));
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

    protected function parsePNUM16()
    {
        $_position = $this->position;

        if (isset($this->cache['PNUM16'][$_position])) {
            $_success = $this->cache['PNUM16'][$_position]['success'];
            $this->position = $this->cache['PNUM16'][$_position]['position'];
            $this->value = $this->cache['PNUM16'][$_position]['value'];

            return $_success;
        }

        $_position195 = $this->position;

        $_value194 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value194[] = $this->value;

            $_success = $this->parseA5();
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
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new Number($this->lineNr, trim($nr), 'PayerNumber');
            });
        }

        $this->cache['PNUM16'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PNUM16');
        }

        return $_success;
    }

    protected function parseREPS3()
    {
        $_position = $this->position;

        if (isset($this->cache['REPS3'][$_position])) {
            $_success = $this->cache['REPS3'][$_position]['success'];
            $this->position = $this->cache['REPS3'][$_position]['position'];
            $this->value = $this->cache['REPS3'][$_position]['value'];

            return $_success;
        }

        $_position197 = $this->position;

        $_value196 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value196[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value196[] = $this->value;

            $this->value = $_value196;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position197, $this->position - $_position197));
        }

        if ($_success) {
            $repetitions = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$repetitions) {
                return new Number($this->lineNr, trim($repetitions), 'Repetitions');
            });
        }

        $this->cache['REPS3'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REPS3');
        }

        return $_success;
    }

    protected function parseSERIAL5()
    {
        $_position = $this->position;

        if (isset($this->cache['SERIAL5'][$_position])) {
            $_success = $this->cache['SERIAL5'][$_position]['success'];
            $this->position = $this->cache['SERIAL5'][$_position]['position'];
            $this->value = $this->cache['SERIAL5'][$_position]['value'];

            return $_success;
        }

        $_position198 = $this->position;

        $_success = $this->parseA5();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position198, $this->position - $_position198));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, trim($integer), 'Serial');
            });
        }

        $this->cache['SERIAL5'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'SERIAL5');
        }

        return $_success;
    }

    protected function parseREF16()
    {
        $_position = $this->position;

        if (isset($this->cache['REF16'][$_position])) {
            $_success = $this->cache['REF16'][$_position]['success'];
            $this->position = $this->cache['REF16'][$_position]['position'];
            $this->value = $this->cache['REF16'][$_position]['value'];

            return $_success;
        }

        $_position232 = $this->position;

        $_value231 = array();

        $_position199 = $this->position;
        $_cut200 = $this->cut;

        $this->cut = false;
        $_success = $this->parseA();

        if (!$_success && !$this->cut) {
            $_success = true;
            $this->position = $_position199;
            $this->value = null;
        }

        $this->cut = $_cut200;

        if ($_success) {
            $_value231[] = $this->value;

            $_position201 = $this->position;
            $_cut202 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position201;
                $this->value = null;
            }

            $this->cut = $_cut202;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position203 = $this->position;
            $_cut204 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position203;
                $this->value = null;
            }

            $this->cut = $_cut204;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position205 = $this->position;
            $_cut206 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position205;
                $this->value = null;
            }

            $this->cut = $_cut206;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position207 = $this->position;
            $_cut208 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position207;
                $this->value = null;
            }

            $this->cut = $_cut208;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position209 = $this->position;
            $_cut210 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position209;
                $this->value = null;
            }

            $this->cut = $_cut210;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position211 = $this->position;
            $_cut212 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position211;
                $this->value = null;
            }

            $this->cut = $_cut212;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position213 = $this->position;
            $_cut214 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position213;
                $this->value = null;
            }

            $this->cut = $_cut214;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position215 = $this->position;
            $_cut216 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position215;
                $this->value = null;
            }

            $this->cut = $_cut216;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position217 = $this->position;
            $_cut218 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position217;
                $this->value = null;
            }

            $this->cut = $_cut218;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position219 = $this->position;
            $_cut220 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position219;
                $this->value = null;
            }

            $this->cut = $_cut220;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position221 = $this->position;
            $_cut222 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position221;
                $this->value = null;
            }

            $this->cut = $_cut222;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position223 = $this->position;
            $_cut224 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position223;
                $this->value = null;
            }

            $this->cut = $_cut224;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position225 = $this->position;
            $_cut226 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position225;
                $this->value = null;
            }

            $this->cut = $_cut226;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position227 = $this->position;
            $_cut228 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position227;
                $this->value = null;
            }

            $this->cut = $_cut228;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $_position229 = $this->position;
            $_cut230 = $this->cut;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position229;
                $this->value = null;
            }

            $this->cut = $_cut230;
        }

        if ($_success) {
            $_value231[] = $this->value;

            $this->value = $_value231;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position232, $this->position - $_position232));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new Text($this->lineNr, trim($text), 'Reference');
            });
        }

        $this->cache['REF16'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REF16');
        }

        return $_success;
    }

    protected function parseINT1()
    {
        $_position = $this->position;

        if (isset($this->cache['INT1'][$_position])) {
            $_success = $this->cache['INT1'][$_position]['success'];
            $this->position = $this->cache['INT1'][$_position]['position'];
            $this->value = $this->cache['INT1'][$_position]['value'];

            return $_success;
        }

        $_position233 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position233, $this->position - $_position233));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, trim($integer));
            });
        }

        $this->cache['INT1'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'INT1');
        }

        return $_success;
    }

    protected function parseINT2()
    {
        $_position = $this->position;

        if (isset($this->cache['INT2'][$_position])) {
            $_success = $this->cache['INT2'][$_position]['success'];
            $this->position = $this->cache['INT2'][$_position]['position'];
            $this->value = $this->cache['INT2'][$_position]['value'];

            return $_success;
        }

        $_position235 = $this->position;

        $_value234 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value234[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value234[] = $this->value;

            $this->value = $_value234;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position235, $this->position - $_position235));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, trim($integer));
            });
        }

        $this->cache['INT2'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'INT2');
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

        $_position236 = $this->position;

        $_success = $this->parseA5();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position236, $this->position - $_position236));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, trim($integer));
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

        $_position238 = $this->position;

        $_value237 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value237[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value237[] = $this->value;

            $this->value = $_value237;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position238, $this->position - $_position238));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, trim($integer));
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

        $_position240 = $this->position;

        $_value239 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value239[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value239[] = $this->value;

            $this->value = $_value239;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position240, $this->position - $_position240));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, trim($integer));
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

        $_position242 = $this->position;

        $_value241 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value241[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value241[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value241[] = $this->value;

            $this->value = $_value241;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position242, $this->position - $_position242));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, trim($integer));
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

        $_position244 = $this->position;

        $_value243 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value243[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value243[] = $this->value;

            $this->value = $_value243;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position244, $this->position - $_position244));
        }

        if ($_success) {
            $integer = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$integer) {
                return new Number($this->lineNr, trim($integer));
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

    protected function parseA()
    {
        $_position = $this->position;

        if (isset($this->cache['A'][$_position])) {
            $_success = $this->cache['A'][$_position]['success'];
            $this->position = $this->cache['A'][$_position]['position'];
            $this->value = $this->cache['A'][$_position]['value'];

            return $_success;
        }

        $_value247 = array();

        $_position245 = $this->position;
        $_cut246 = $this->cut;

        $this->cut = false;
        $_success = $this->parseEOL();

        if (!$_success) {
            $_success = true;
            $this->value = null;
        } else {
            $_success = false;
        }

        $this->position = $_position245;
        $this->cut = $_cut246;

        if ($_success) {
            $_value247[] = $this->value;

            if ($this->position < strlen($this->string)) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }
        }

        if ($_success) {
            $_value247[] = $this->value;

            $this->value = $_value247;
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

        $_position249 = $this->position;

        $_value248 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value248[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value248[] = $this->value;

            $this->value = $_value248;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position249, $this->position - $_position249));
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

        $_position251 = $this->position;

        $_value250 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value250[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value250[] = $this->value;

            $this->value = $_value250;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position251, $this->position - $_position251));
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

        $_position253 = $this->position;

        $_value252 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value252[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value252[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value252[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value252[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value252[] = $this->value;

            $this->value = $_value252;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position253, $this->position - $_position253));
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

        $_position255 = $this->position;

        $_value254 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value254[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value254[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value254[] = $this->value;

            $this->value = $_value254;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position255, $this->position - $_position255));
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

        $_position257 = $this->position;

        $_value256 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value256[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value256[] = $this->value;

            $this->value = $_value256;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position257, $this->position - $_position257));
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

    protected function parseA40()
    {
        $_position = $this->position;

        if (isset($this->cache['A40'][$_position])) {
            $_success = $this->cache['A40'][$_position]['success'];
            $this->position = $this->cache['A40'][$_position]['position'];
            $this->value = $this->cache['A40'][$_position]['value'];

            return $_success;
        }

        $_position259 = $this->position;

        $_value258 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value258[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value258[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value258[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value258[] = $this->value;

            $this->value = $_value258;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position259, $this->position - $_position259));
        }

        $this->cache['A40'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'A40');
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

    protected function parseS5()
    {
        $_position = $this->position;

        if (isset($this->cache['S5'][$_position])) {
            $_success = $this->cache['S5'][$_position]['success'];
            $this->position = $this->cache['S5'][$_position]['position'];
            $this->value = $this->cache['S5'][$_position]['value'];

            return $_success;
        }

        $_position261 = $this->position;

        $_value260 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value260[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value260[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value260[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value260[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value260[] = $this->value;

            $this->value = $_value260;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position261, $this->position - $_position261));
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

        $_position263 = $this->position;

        $_value262 = array();

        $_success = $this->parseS5();

        if ($_success) {
            $_value262[] = $this->value;

            $_success = $this->parseS5();
        }

        if ($_success) {
            $_value262[] = $this->value;

            $this->value = $_value262;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position263, $this->position - $_position263));
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

        $_position265 = $this->position;

        $_value264 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value264[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value264[] = $this->value;

            $this->value = $_value264;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position265, $this->position - $_position265));
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

        $_value271 = array();

        $_value267 = array();
        $_cut268 = $this->cut;

        while (true) {
            $_position266 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success) {
                break;
            }

            $_value267[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position266;
            $this->value = $_value267;
        }

        $this->cut = $_cut268;

        if ($_success) {
            $_value271[] = $this->value;

            $_position269 = $this->position;
            $_cut270 = $this->cut;

            $this->cut = false;
            $_success = $this->parseEOL();

            if (!$_success && !$this->cut) {
                $this->position = $_position269;

                $_success = $this->parseEOF();
            }

            $this->cut = $_cut270;
        }

        if ($_success) {
            $_value271[] = $this->value;

            $this->value = $_value271;
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

        $_value274 = array();

        $_position272 = $this->position;
        $_cut273 = $this->cut;

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
            $this->position = $_position272;
            $this->value = null;
        }

        $this->cut = $_cut273;

        if ($_success) {
            $_value274[] = $this->value;

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
            $_value274[] = $this->value;

            $this->value = $_value274;
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

        $_position275 = $this->position;
        $_cut276 = $this->cut;

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

        $this->position = $_position275;
        $this->cut = $_cut276;

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

        $_value280 = array();
        $_cut281 = $this->cut;

        while (true) {
            $_position279 = $this->position;

            $this->cut = false;
            $_position277 = $this->position;
            $_cut278 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS();

            if (!$_success && !$this->cut) {
                $this->position = $_position277;

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
                $this->position = $_position277;

                $_success = $this->parseEOL();
            }

            $this->cut = $_cut278;

            if (!$_success) {
                break;
            }

            $_value280[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position279;
            $this->value = $_value280;
        }

        $this->cut = $_cut281;

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