<?php

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Layouts;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\ImmediateDateNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\Record\Request;
use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Tree\ReferredAccountNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
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
        $_success = $this->parseREQ_MANDATE_LAYOUT();

        if (!$_success && !$this->cut) {
            $this->position = $_position4;

            $_success = $this->parseREQ_PAYMENT_LAYOUT();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position4;

            $_success = $this->parseREQ_AMENDMENT_LAYOUT();
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
                $_success = $this->parseREQ_MANDATE_LAYOUT();

                if (!$_success && !$this->cut) {
                    $this->position = $_position4;

                    $_success = $this->parseREQ_PAYMENT_LAYOUT();
                }

                if (!$_success && !$this->cut) {
                    $this->position = $_position4;

                    $_success = $this->parseREQ_AMENDMENT_LAYOUT();
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
            $layouts = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$layouts) {
                return new FileNode(...$layouts);
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

    protected function parseREQ_OPENING_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_OPENING_RECORD'][$_position])) {
            $_success = $this->cache['REQ_OPENING_RECORD'][$_position]['success'];
            $this->position = $this->cache['REQ_OPENING_RECORD'][$_position]['position'];
            $this->value = $this->cache['REQ_OPENING_RECORD'][$_position]['value'];

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
                $payeeBgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value9[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value9[] = $this->value;

            $this->value = $_value9;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$payeeBgcNr, &$payeeBg, &$void) {
                return new Request\RequestOpeningRecordNode(
                    $this->lineNr,
                    $date,
                    new TextNode($this->lineNr + 1, 'AUTOGIRO'),
                    new TextNode($this->lineNr + 1, str_repeat(' ', 44)),
                    $payeeBgcNr,
                    $payeeBg,
                    $void
                );
            });
        }

        $this->cache['REQ_OPENING_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_OPENING_RECORD');
        }

        return $_success;
    }

    protected function parseREQ_MANDATE_LAYOUT()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_MANDATE_LAYOUT'][$_position])) {
            $_success = $this->cache['REQ_MANDATE_LAYOUT'][$_position]['success'];
            $this->position = $this->cache['REQ_MANDATE_LAYOUT'][$_position]['position'];
            $this->value = $this->cache['REQ_MANDATE_LAYOUT'][$_position]['value'];

            return $_success;
        }

        $_value15 = array();

        $_success = $this->parseREQ_OPENING_RECORD();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value15[] = $this->value;

            $_position10 = $this->position;
            $_cut11 = $this->cut;

            $this->cut = false;
            $_success = $this->parseREQ_DEL_MANDATE_RECORD();

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_REJECT_MANDATE_RECORD();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_CREATE_MANDATE_RECORD();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position10;

                $_success = $this->parseREQ_UPDATE_MANDATE_RECORD();
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
                    $_success = $this->parseREQ_DEL_MANDATE_RECORD();

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_REJECT_MANDATE_RECORD();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_CREATE_MANDATE_RECORD();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position10;

                        $_success = $this->parseREQ_UPDATE_MANDATE_RECORD();
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
                return new LayoutNode(Layouts::LAYOUT_MANDATE_REQUEST, $open, ...$records);
            });
        }

        $this->cache['REQ_MANDATE_LAYOUT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_MANDATE_LAYOUT');
        }

        return $_success;
    }

    protected function parseREQ_DEL_MANDATE_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_DEL_MANDATE_RECORD'][$_position])) {
            $_success = $this->cache['REQ_DEL_MANDATE_RECORD'][$_position]['success'];
            $this->position = $this->cache['REQ_DEL_MANDATE_RECORD'][$_position]['position'];
            $this->value = $this->cache['REQ_DEL_MANDATE_RECORD'][$_position]['value'];

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

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
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

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value16[] = $this->value;

            $this->value = $_value16;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$payeeBg, &$payerNr, &$void) {
                return new Request\DeleteMandateRequestNode($this->lineNr, $payeeBg, $payerNr, $void);
            });
        }

        $this->cache['REQ_DEL_MANDATE_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_DEL_MANDATE_RECORD');
        }

        return $_success;
    }

    protected function parseREQ_REJECT_MANDATE_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_REJECT_MANDATE_RECORD'][$_position])) {
            $_success = $this->cache['REQ_REJECT_MANDATE_RECORD'][$_position]['success'];
            $this->position = $this->cache['REQ_REJECT_MANDATE_RECORD'][$_position]['position'];
            $this->value = $this->cache['REQ_REJECT_MANDATE_RECORD'][$_position]['value'];

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

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
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

            $_success = $this->parseTEXT48();

            if ($_success) {
                $space = $this->value;
            }
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

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value17[] = $this->value;

            $this->value = $_value17;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$payeeBg, &$payerNr, &$space, &$void) {
                return new Request\RejectDigitalMandateRequestNode($this->lineNr, $payeeBg, $payerNr, $space, new TextNode($this->lineNr, 'AV'), $void);
            });
        }

        $this->cache['REQ_REJECT_MANDATE_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_REJECT_MANDATE_RECORD');
        }

        return $_success;
    }

    protected function parseREQ_CREATE_MANDATE_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_CREATE_MANDATE_RECORD'][$_position])) {
            $_success = $this->cache['REQ_CREATE_MANDATE_RECORD'][$_position]['success'];
            $this->position = $this->cache['REQ_CREATE_MANDATE_RECORD'][$_position]['position'];
            $this->value = $this->cache['REQ_CREATE_MANDATE_RECORD'][$_position]['value'];

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

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
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
            $_success = $this->parseACCOUNT();

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

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $this->value = $_value22;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$payeeBg, &$payerNr, &$account, &$id, &$void) {
                return $id && trim($id->getValue())
                    ? new Request\CreateMandateRequestNode($this->lineNr, $payeeBg, $payerNr, $account, $id, $void)
                    : new Request\AcceptDigitalMandateRequestNode($this->lineNr, $payeeBg, $payerNr, $void);
            });
        }

        $this->cache['REQ_CREATE_MANDATE_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_CREATE_MANDATE_RECORD');
        }

        return $_success;
    }

    protected function parseREQ_UPDATE_MANDATE_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_UPDATE_MANDATE_RECORD'][$_position])) {
            $_success = $this->cache['REQ_UPDATE_MANDATE_RECORD'][$_position]['success'];
            $this->position = $this->cache['REQ_UPDATE_MANDATE_RECORD'][$_position]['position'];
            $this->value = $this->cache['REQ_UPDATE_MANDATE_RECORD'][$_position]['value'];

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

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $oldPayeeBg = $this->value;
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

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $newPayeeBg = $this->value;
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

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $this->value = $_value23;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$oldPayeeBg, &$oldPayerNr, &$newPayeeBg, &$newPayerNr, &$void) {
                return new Request\UpdateMandateRequestNode($this->lineNr, $oldPayeeBg, $oldPayerNr, $newPayeeBg, $newPayerNr, $void);
            });
        }

        $this->cache['REQ_UPDATE_MANDATE_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_UPDATE_MANDATE_RECORD');
        }

        return $_success;
    }

    protected function parseREQ_PAYMENT_LAYOUT()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_PAYMENT_LAYOUT'][$_position])) {
            $_success = $this->cache['REQ_PAYMENT_LAYOUT'][$_position]['success'];
            $this->position = $this->cache['REQ_PAYMENT_LAYOUT'][$_position]['position'];
            $this->value = $this->cache['REQ_PAYMENT_LAYOUT'][$_position]['value'];

            return $_success;
        }

        $_value27 = array();

        $_success = $this->parseREQ_OPENING_RECORD();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parseREQ_TRANSACTION_RECORD();

            if ($_success) {
                $_value25 = array($this->value);
                $_cut26 = $this->cut;

                while (true) {
                    $_position24 = $this->position;

                    $this->cut = false;
                    $_success = $this->parseREQ_TRANSACTION_RECORD();

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
                return new LayoutNode(Layouts::LAYOUT_PAYMENT_REQUEST, $open, ...$records);
            });
        }

        $this->cache['REQ_PAYMENT_LAYOUT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_PAYMENT_LAYOUT');
        }

        return $_success;
    }

    protected function parseREQ_TRANSACTION_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_TRANSACTION_RECORD'][$_position])) {
            $_success = $this->cache['REQ_TRANSACTION_RECORD'][$_position]['success'];
            $this->position = $this->cache['REQ_TRANSACTION_RECORD'][$_position]['position'];
            $this->value = $this->cache['REQ_TRANSACTION_RECORD'][$_position]['value'];

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

            $_success = $this->parseVOID();

            if ($_success) {
                $space = $this->value;
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

            $_success = $this->parseAMOUNT();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseVARIABLE_TEXT();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $this->value = $_value32;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$tc, &$date, &$ival, &$reps, &$space, &$payerNr, &$amount, &$payeeBg, &$ref, &$void) {
                return $tc == '82'
                    ? new Request\IncomingTransactionRequestNode($this->lineNr, $date, $ival, $reps, $space, $payerNr, $amount, $payeeBg, $ref, $void)
                    : new Request\OutgoingTransactionRequestNode($this->lineNr, $date, $ival, $reps, $space, $payerNr, $amount, $payeeBg, $ref, $void);
            });
        }

        $this->cache['REQ_TRANSACTION_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_TRANSACTION_RECORD');
        }

        return $_success;
    }

    protected function parseREQ_AMENDMENT_LAYOUT()
    {
        $_position = $this->position;

        if (isset($this->cache['REQ_AMENDMENT_LAYOUT'][$_position])) {
            $_success = $this->cache['REQ_AMENDMENT_LAYOUT'][$_position]['success'];
            $this->position = $this->cache['REQ_AMENDMENT_LAYOUT'][$_position]['position'];
            $this->value = $this->cache['REQ_AMENDMENT_LAYOUT'][$_position]['value'];

            return $_success;
        }

        $_value36 = array();

        $_success = $this->parseREQ_OPENING_RECORD();

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
                // TODO this is just a stub...
                return new LayoutNode(Layouts::LAYOUT_AMENDMENT_REQUEST, $open, ...$records);
            });
        }

        $this->cache['REQ_AMENDMENT_LAYOUT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REQ_AMENDMENT_LAYOUT');
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

        $_value40 = array();

        $_success = $this->parseRESP_PAYMENT_OPENING_RECORD();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value40[] = $this->value;

            $_value38 = array();
            $_cut39 = $this->cut;

            while (true) {
                $_position37 = $this->position;

                $this->cut = false;
                $_success = $this->parseRESP_MANDATE_RECORD();

                if (!$_success) {
                    break;
                }

                $_value38[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position37;
                $this->value = $_value38;
            }

            $this->cut = $_cut39;

            if ($_success) {
                $mands = $this->value;
            }
        }

        if ($_success) {
            $_value40[] = $this->value;

            $_success = $this->parseRESP_MANDATE_CLOSING_RECORD();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value40[] = $this->value;

            $this->value = $_value40;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$mands, &$close) {
                return new FileNode(new LayoutNode(Layouts::LAYOUT_PAYMENT_RESPONSE, $open, $close, ...$mands));
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

    protected function parseRESP_PAYMENT_OPENING_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_PAYMENT_OPENING_RECORD'][$_position])) {
            $_success = $this->cache['RESP_PAYMENT_OPENING_RECORD'][$_position]['success'];
            $this->position = $this->cache['RESP_PAYMENT_OPENING_RECORD'][$_position]['position'];
            $this->value = $this->cache['RESP_PAYMENT_OPENING_RECORD'][$_position]['value'];

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

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_position42 = $this->position;

            $_value41 = array();

            $_success = $this->parseA10();

            if ($_success) {
                $_value41[] = $this->value;

                $_success = $this->parseA10();
            }

            if ($_success) {
                $_value41[] = $this->value;

                $this->value = $_value41;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position42, $this->position - $_position42));
            }

            if ($_success) {
                $layout = $this->value;
            }
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $payeeBgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value43[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value43[] = $this->value;

            $this->value = $_value43;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$layout, &$payeeBgcNr, &$payeeBg, &$void) {
                // TODO this is not valid!!
                    // DATETIME
                    // $layout ID text...

                return new Record\ResponseOpeningRecord($this->lineNr, $date, $payeeBgcNr, $payeeBg, $void);
            });
        }

        $this->cache['RESP_PAYMENT_OPENING_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_PAYMENT_OPENING_RECORD');
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

        $_value49 = array();

        $_position44 = $this->position;
        $_cut45 = $this->cut;

        $this->cut = false;
        $_success = $this->parseRESP_MANDATE_OPENING_OLD_RECORD();

        if (!$_success && !$this->cut) {
            $this->position = $_position44;

            $_success = $this->parseRESP_MANDATE_OPENING_RECORD();
        }

        $this->cut = $_cut45;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_value47 = array();
            $_cut48 = $this->cut;

            while (true) {
                $_position46 = $this->position;

                $this->cut = false;
                $_success = $this->parseRESP_MANDATE_RECORD();

                if (!$_success) {
                    break;
                }

                $_value47[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position46;
                $this->value = $_value47;
            }

            $this->cut = $_cut48;

            if ($_success) {
                $mands = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseRESP_MANDATE_CLOSING_RECORD();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $this->value = $_value49;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$mands, &$close) {
                return new FileNode(new LayoutNode(Layouts::LAYOUT_MANDATE_RESPONSE, $open, $close, ...$mands));
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

    protected function parseRESP_MANDATE_OPENING_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_OPENING_RECORD'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_OPENING_RECORD'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_OPENING_RECORD'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_OPENING_RECORD'][$_position]['value'];

            return $_success;
        }

        $_value50 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value50[] = $this->value;

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
            $_value50[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseS4();
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value50[] = $this->value;

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
            $_value50[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $payeeBgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $this->value = $_value50;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$payeeBgcNr, &$payeeBg, &$void) {
                return new Record\ResponseOpeningRecord($this->lineNr, $date, $payeeBgcNr, $payeeBg, $void);
            });
        }

        $this->cache['RESP_MANDATE_OPENING_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_OPENING_RECORD');
        }

        return $_success;
    }

    protected function parseRESP_MANDATE_OPENING_OLD_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_OPENING_OLD_RECORD'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_OPENING_OLD_RECORD'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_OPENING_OLD_RECORD'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_OPENING_OLD_RECORD'][$_position]['value'];

            return $_success;
        }

        $_value51 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
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
            $_value51[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value51[] = $this->value;

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
            $_value51[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value51[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value51[] = $this->value;

            $this->value = $_value51;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$payeeBg, &$void) {
                return new Record\ResponseOpeningRecord($this->lineNr, $date, new PayeeBgcNumberNode($this->lineNr, ''), $payeeBg, $void);
            });
        }

        $this->cache['RESP_MANDATE_OPENING_OLD_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_OPENING_OLD_RECORD');
        }

        return $_success;
    }

    protected function parseRESP_MANDATE_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_RECORD'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_RECORD'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_RECORD'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_RECORD'][$_position]['value'];

            return $_success;
        }

        $_value58 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
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

            $_success = $this->parseACCOUNT();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_position52 = $this->position;
            $_cut53 = $this->cut;

            $this->cut = false;
            $_success = $this->parseS5();

            if (!$_success && !$this->cut) {
                $this->position = $_position52;

                if (substr($this->string, $this->position, strlen('00000')) === '00000') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen('00000'));
                    $this->position += strlen('00000');
                } else {
                    $_success = false;

                    $this->report($this->position, '\'00000\'');
                }
            }

            $this->cut = $_cut53;
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseMESSAGE();

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseMESSAGE();

            if ($_success) {
                $status = $this->value;
            }
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

            $_position57 = $this->position;

            $_position55 = $this->position;
            $_cut56 = $this->cut;

            $this->cut = false;
            $_value54 = array();

            $_success = $this->parseA5();

            if ($_success) {
                $_value54[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value54[] = $this->value;

                $this->value = $_value54;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position55;
                $this->value = null;
            }

            $this->cut = $_cut56;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position57, $this->position - $_position57));
            }

            if ($_success) {
                $validDate = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value58[] = $this->value;

            $this->value = $_value58;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$payeeBg, &$payerNr, &$account, &$id, &$info, &$status, &$date, &$validDate, &$void) {
                // If account is empty a valid bankgiro number may be read from the payer number field
                if (!trim($account->getValue())) {
                    $account = new ReferredAccountNode($account->getLineNr(), $payerNr->getValue());
                }

                // A mandate-valid-from-date is only present in the old layout
                if ($validDate) {
                    array_unshift($void, new TextNode($this->lineNr, (string)$validDate));
                }

                $info->setAttribute('message_id', "73.info.{$info->getValue()}");
                $status->setAttribute('message_id', "73.status.{$status->getValue()}");

                return new Record\MandateResponseRecord($this->lineNr, $payeeBg, $payerNr, $account, $id, $info, $status, $date, $void);
            });
        }

        $this->cache['RESP_MANDATE_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_RECORD');
        }

        return $_success;
    }

    protected function parseRESP_MANDATE_CLOSING_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['RESP_MANDATE_CLOSING_RECORD'][$_position])) {
            $_success = $this->cache['RESP_MANDATE_CLOSING_RECORD'][$_position]['success'];
            $this->position = $this->cache['RESP_MANDATE_CLOSING_RECORD'][$_position]['position'];
            $this->value = $this->cache['RESP_MANDATE_CLOSING_RECORD'][$_position]['value'];

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

            $_success = $this->parseDATE();

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

            $_success = $this->parseINT7();

            if ($_success) {
                $nrOfPosts = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

            $this->value = $_value59;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrOfPosts, &$void) {
                return new Record\MandateResponseClosingRecord($this->lineNr, $date, $nrOfPosts, $void);
            });
        }

        $this->cache['RESP_MANDATE_CLOSING_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RESP_MANDATE_CLOSING_RECORD');
        }

        return $_success;
    }

    protected function parseACCOUNT()
    {
        $_position = $this->position;

        if (isset($this->cache['ACCOUNT'][$_position])) {
            $_success = $this->cache['ACCOUNT'][$_position]['success'];
            $this->position = $this->cache['ACCOUNT'][$_position]['position'];
            $this->value = $this->cache['ACCOUNT'][$_position]['value'];

            return $_success;
        }

        $_position61 = $this->position;

        $_value60 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value60[] = $this->value;

            $this->value = $_value60;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position61, $this->position - $_position61));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new AccountNode($this->lineNr + 1, $number);
            });
        }

        $this->cache['ACCOUNT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'ACCOUNT');
        }

        return $_success;
    }

    protected function parseAMOUNT()
    {
        $_position = $this->position;

        if (isset($this->cache['AMOUNT'][$_position])) {
            $_success = $this->cache['AMOUNT'][$_position]['success'];
            $this->position = $this->cache['AMOUNT'][$_position]['position'];
            $this->value = $this->cache['AMOUNT'][$_position]['value'];

            return $_success;
        }

        $_position63 = $this->position;

        $_value62 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value62[] = $this->value;

            $this->value = $_value62;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position63, $this->position - $_position63));
        }

        if ($_success) {
            $amount = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$amount) {
                return new AmountNode($this->lineNr + 1, $amount);
            });
        }

        $this->cache['AMOUNT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AMOUNT');
        }

        return $_success;
    }

    protected function parsePAYEE_BG()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYEE_BG'][$_position])) {
            $_success = $this->cache['PAYEE_BG'][$_position]['success'];
            $this->position = $this->cache['PAYEE_BG'][$_position]['position'];
            $this->value = $this->cache['PAYEE_BG'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseA10();

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new PayeeBankgiroNode($this->lineNr + 1, $number);
            });
        }

        $this->cache['PAYEE_BG'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'PAYEE_BG');
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

        $_position65 = $this->position;

        $_value64 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $this->value = $_value64;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position65, $this->position - $_position65));
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

        $_position67 = $this->position;

        $_value66 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value66[] = $this->value;

            $this->value = $_value66;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position67, $this->position - $_position67));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new PayeeBgcNumberNode($this->lineNr + 1, $nr);
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

        $_position69 = $this->position;

        $_value68 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value68[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value68[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value68[] = $this->value;

            $this->value = $_value68;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position69, $this->position - $_position69));
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

    protected function parseINTERVAL()
    {
        $_position = $this->position;

        if (isset($this->cache['INTERVAL'][$_position])) {
            $_success = $this->cache['INTERVAL'][$_position]['success'];
            $this->position = $this->cache['INTERVAL'][$_position]['position'];
            $this->value = $this->cache['INTERVAL'][$_position]['value'];

            return $_success;
        }

        $_position70 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position70, $this->position - $_position70));
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

    protected function parseMESSAGE()
    {
        $_position = $this->position;

        if (isset($this->cache['MESSAGE'][$_position])) {
            $_success = $this->cache['MESSAGE'][$_position]['success'];
            $this->position = $this->cache['MESSAGE'][$_position]['position'];
            $this->value = $this->cache['MESSAGE'][$_position]['value'];

            return $_success;
        }

        $_position72 = $this->position;

        $_value71 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value71[] = $this->value;

            $this->value = $_value71;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position72, $this->position - $_position72));
        }

        if ($_success) {
            $msg = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$msg) {
                return new MessageNode($this->lineNr + 1, $msg);
            });
        }

        $this->cache['MESSAGE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MESSAGE');
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

        $_position74 = $this->position;

        $_value73 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value73[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value73[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value73[] = $this->value;

            $this->value = $_value73;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position74, $this->position - $_position74));
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

        $_position76 = $this->position;

        $_value75 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value75[] = $this->value;

            $this->value = $_value75;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position76, $this->position - $_position76));
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

    protected function parseINT7()
    {
        $_position = $this->position;

        if (isset($this->cache['INT7'][$_position])) {
            $_success = $this->cache['INT7'][$_position]['success'];
            $this->position = $this->cache['INT7'][$_position]['position'];
            $this->value = $this->cache['INT7'][$_position]['value'];

            return $_success;
        }

        $_position78 = $this->position;

        $_value77 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value77[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value77[] = $this->value;

            $this->value = $_value77;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position78, $this->position - $_position78));
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

    protected function parseVARIABLE_TEXT()
    {
        $_position = $this->position;

        if (isset($this->cache['VARIABLE_TEXT'][$_position])) {
            $_success = $this->cache['VARIABLE_TEXT'][$_position]['success'];
            $this->position = $this->cache['VARIABLE_TEXT'][$_position]['position'];
            $this->value = $this->cache['VARIABLE_TEXT'][$_position]['value'];

            return $_success;
        }

        $_position82 = $this->position;

        $_value80 = array();
        $_cut81 = $this->cut;

        while (true) {
            $_position79 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success) {
                break;
            }

            $_value80[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position79;
            $this->value = $_value80;
        }

        $this->cut = $_cut81;

        if ($_success) {
            $this->value = strval(substr($this->string, $_position82, $this->position - $_position82));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text);
            });
        }

        $this->cache['VARIABLE_TEXT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'VARIABLE_TEXT');
        }

        return $_success;
    }

    protected function parseTEXT48()
    {
        $_position = $this->position;

        if (isset($this->cache['TEXT48'][$_position])) {
            $_success = $this->cache['TEXT48'][$_position]['success'];
            $this->position = $this->cache['TEXT48'][$_position]['position'];
            $this->value = $this->cache['TEXT48'][$_position]['value'];

            return $_success;
        }

        $_position84 = $this->position;

        $_value83 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value83[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value83[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value83[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value83[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value83[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value83[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value83[] = $this->value;

            $this->value = $_value83;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position84, $this->position - $_position84));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text);
            });
        }

        $this->cache['TEXT48'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'TEXT48');
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

        $_position85 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position85, $this->position - $_position85));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text, '/^ $/');
            });
        }

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

        $_position87 = $this->position;

        $_value86 = array();

        $_success = $this->parseA();

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

        $_position89 = $this->position;

        $_value88 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value88[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value88[] = $this->value;

            $this->value = $_value88;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position89, $this->position - $_position89));
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

        $_position91 = $this->position;

        $_value90 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value90[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value90[] = $this->value;

            $this->value = $_value90;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position91, $this->position - $_position91));
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

        $_value92 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value92[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value92[] = $this->value;

            $this->value = $_value92;
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

        $_value93 = array();

        $_success = $this->parseS2();

        if ($_success) {
            $_value93[] = $this->value;

            $_success = $this->parseS2();
        }

        if ($_success) {
            $_value93[] = $this->value;

            $this->value = $_value93;
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

        $_value94 = array();

        $_success = $this->parseS4();

        if ($_success) {
            $_value94[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value94[] = $this->value;

            $this->value = $_value94;
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

        $_value95 = array();

        $_success = $this->parseS5();

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseS5();
        }

        if ($_success) {
            $_value95[] = $this->value;

            $this->value = $_value95;
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

        $_value96 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value96[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value96[] = $this->value;

            $this->value = $_value96;
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

        $_value102 = array();

        $_value98 = array();
        $_cut99 = $this->cut;

        while (true) {
            $_position97 = $this->position;

            $this->cut = false;
            $_success = $this->parseVOID();

            if (!$_success) {
                break;
            }

            $_value98[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position97;
            $this->value = $_value98;
        }

        $this->cut = $_cut99;

        if ($_success) {
            $void = $this->value;
        }

        if ($_success) {
            $_value102[] = $this->value;

            $_position100 = $this->position;
            $_cut101 = $this->cut;

            $this->cut = false;
            $_success = $this->parseEOL();

            if (!$_success && !$this->cut) {
                $this->position = $_position100;

                $_success = $this->parseEOF();
            }

            $this->cut = $_cut101;
        }

        if ($_success) {
            $_value102[] = $this->value;

            $this->value = $_value102;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$void) {
                return $void;
            });
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

        $_value105 = array();

        $_position103 = $this->position;
        $_cut104 = $this->cut;

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
            $this->position = $_position103;
            $this->value = null;
        }

        $this->cut = $_cut104;

        if ($_success) {
            $_value105[] = $this->value;

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
            $_value105[] = $this->value;

            $this->value = $_value105;
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

        $_position106 = $this->position;
        $_cut107 = $this->cut;

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

        $this->position = $_position106;
        $this->cut = $_cut107;

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