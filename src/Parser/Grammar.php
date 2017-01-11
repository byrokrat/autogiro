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
use byrokrat\autogiro\Tree\Record\ClosingRecordNode;
use byrokrat\autogiro\Tree\Record\OpeningRecordNode;
use byrokrat\autogiro\Tree\Record\Request;
use byrokrat\autogiro\Tree\Record\Response;
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

                $_success = $this->parseL_RESP_MANDATE();
            }

            $this->cut = $_cut2;

            if ($_success) {
                $layout = $this->value;
            }
        }

        if ($_success) {
            $_value3[] = $this->value;

            $this->value = $_value3;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$layout) {
                return $layout;
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

    protected function parseR_GENERIC_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['R_GENERIC_OPENING'][$_position])) {
            $_success = $this->cache['R_GENERIC_OPENING'][$_position]['success'];
            $this->position = $this->cache['R_GENERIC_OPENING'][$_position]['position'];
            $this->value = $this->cache['R_GENERIC_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value4 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseAUTOGIRO();

            if ($_success) {
                $ag = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseVOID14();

            if ($_success) {
                $space1 = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseVOID12();

            if ($_success) {
                $space2 = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseTEXT20();

            if ($_success) {
                $layout = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $payeeBgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $this->value = $_value4;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$ag, &$space1, &$date, &$space2, &$layout, &$payeeBgcNr, &$payeeBg, &$void) {
                return new OpeningRecordNode($this->lineNr, $ag, $space1, $date, $space2, $layout, $payeeBgcNr, $payeeBg, $void);
            });
        }

        $this->cache['R_GENERIC_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_GENERIC_OPENING');
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

        $_position5 = $this->position;
        $_cut6 = $this->cut;

        $this->cut = false;
        $_success = $this->parseL_REQ_MANDATE();

        if (!$_success && !$this->cut) {
            $this->position = $_position5;

            $_success = $this->parseL_REQ_PAYMENT();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position5;

            $_success = $this->parseL_REQ_AMENDMENT();
        }

        $this->cut = $_cut6;

        if ($_success) {
            $_value8 = array($this->value);
            $_cut9 = $this->cut;

            while (true) {
                $_position7 = $this->position;

                $this->cut = false;
                $_position5 = $this->position;
                $_cut6 = $this->cut;

                $this->cut = false;
                $_success = $this->parseL_REQ_MANDATE();

                if (!$_success && !$this->cut) {
                    $this->position = $_position5;

                    $_success = $this->parseL_REQ_PAYMENT();
                }

                if (!$_success && !$this->cut) {
                    $this->position = $_position5;

                    $_success = $this->parseL_REQ_AMENDMENT();
                }

                $this->cut = $_cut6;

                if (!$_success) {
                    break;
                }

                $_value8[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position7;
                $this->value = $_value8;
            }

            $this->cut = $_cut9;
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

    protected function parseR_REQ_OPENING()
    {
        $_position = $this->position;

        if (isset($this->cache['R_REQ_OPENING'][$_position])) {
            $_success = $this->cache['R_REQ_OPENING'][$_position]['success'];
            $this->position = $this->cache['R_REQ_OPENING'][$_position]['position'];
            $this->value = $this->cache['R_REQ_OPENING'][$_position]['value'];

            return $_success;
        }

        $_value10 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseAUTOGIRO();

            if ($_success) {
                $ag = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseVOID44();

            if ($_success) {
                $space = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseBGC_NR();

            if ($_success) {
                $payeeBgcNr = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $this->value = $_value10;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$ag, &$space, &$payeeBgcNr, &$payeeBg, &$void) {
                return new Request\RequestOpeningRecordNode($this->lineNr, $date, $ag, $space, $payeeBgcNr, $payeeBg, $void);
            });
        }

        $this->cache['R_REQ_OPENING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_REQ_OPENING');
        }

        return $_success;
    }

    protected function parseL_REQ_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['L_REQ_MANDATE'][$_position])) {
            $_success = $this->cache['L_REQ_MANDATE'][$_position]['success'];
            $this->position = $this->cache['L_REQ_MANDATE'][$_position]['position'];
            $this->value = $this->cache['L_REQ_MANDATE'][$_position]['value'];

            return $_success;
        }

        $_value16 = array();

        $_success = $this->parseR_REQ_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_position11 = $this->position;
            $_cut12 = $this->cut;

            $this->cut = false;
            $_success = $this->parseR_REQ_CREATE_MANDATE();

            if (!$_success && !$this->cut) {
                $this->position = $_position11;

                $_success = $this->parseR_REQ_UPDATE_MANDATE();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position11;

                $_success = $this->parseR_REQ_DEL_MANDATE();
            }

            $this->cut = $_cut12;

            if ($_success) {
                $_value14 = array($this->value);
                $_cut15 = $this->cut;

                while (true) {
                    $_position13 = $this->position;

                    $this->cut = false;
                    $_position11 = $this->position;
                    $_cut12 = $this->cut;

                    $this->cut = false;
                    $_success = $this->parseR_REQ_CREATE_MANDATE();

                    if (!$_success && !$this->cut) {
                        $this->position = $_position11;

                        $_success = $this->parseR_REQ_UPDATE_MANDATE();
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position11;

                        $_success = $this->parseR_REQ_DEL_MANDATE();
                    }

                    $this->cut = $_cut12;

                    if (!$_success) {
                        break;
                    }

                    $_value14[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position13;
                    $this->value = $_value14;
                }

                $this->cut = $_cut15;
            }

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value16[] = $this->value;

            $this->value = $_value16;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return (new LayoutNode($open, ...$records))->setAttribute('layout_name', Layouts::LAYOUT_MANDATE_REQUEST);
            });
        }

        $this->cache['L_REQ_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'L_REQ_MANDATE');
        }

        return $_success;
    }

    protected function parseR_REQ_CREATE_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['R_REQ_CREATE_MANDATE'][$_position])) {
            $_success = $this->cache['R_REQ_CREATE_MANDATE'][$_position]['success'];
            $this->position = $this->cache['R_REQ_CREATE_MANDATE'][$_position]['position'];
            $this->value = $this->cache['R_REQ_CREATE_MANDATE'][$_position]['value'];

            return $_success;
        }

        $_value25 = array();

        if (substr($this->string, $this->position, strlen('04')) === '04') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('04'));
            $this->position += strlen('04');
        } else {
            $_success = false;

            $this->report($this->position, '\'04\'');
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_position17 = $this->position;
            $_cut18 = $this->cut;

            $this->cut = false;
            $_success = $this->parseACCOUNT();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position17;
                $this->value = null;
            }

            $this->cut = $_cut18;

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_position19 = $this->position;
            $_cut20 = $this->cut;

            $this->cut = false;
            $_success = $this->parseID();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position19;
                $this->value = null;
            }

            $this->cut = $_cut20;

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_position21 = $this->position;
            $_cut22 = $this->cut;

            $this->cut = false;
            $_success = $this->parseVOID20();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position21;
                $this->value = null;
            }

            $this->cut = $_cut22;

            if ($_success) {
                $space = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_position23 = $this->position;
            $_cut24 = $this->cut;

            $this->cut = false;
            if (substr($this->string, $this->position, strlen('AV')) === 'AV') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('AV'));
                $this->position += strlen('AV');
            } else {
                $_success = false;

                $this->report($this->position, '\'AV\'');
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position23;
                $this->value = null;
            }

            $this->cut = $_cut24;

            if ($_success) {
                $reject = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $this->value = $_value25;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$payeeBg, &$payerNr, &$account, &$id, &$space, &$reject, &$void) {
                // TODO $space måste också sparas till objekt för att kunna valideras...
                // TODO och reject??

                new TextNode($this->lineNr, (string)$reject, '/^(AV)|( {0,2})$/');

                if ($reject == 'AV') {
                    return new Request\RejectMandateRequestNode($this->lineNr, $payeeBg, $payerNr, $void);
                }

                return $account && $id
                    ? new Request\CreateMandateRequestNode($this->lineNr, $payeeBg, $payerNr, $account, $id, $void)
                    : new Request\AcceptMandateRequestNode($this->lineNr, $payeeBg, $payerNr, $void);
            });
        }

        $this->cache['R_REQ_CREATE_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_REQ_CREATE_MANDATE');
        }

        return $_success;
    }

    protected function parseR_REQ_UPDATE_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['R_REQ_UPDATE_MANDATE'][$_position])) {
            $_success = $this->cache['R_REQ_UPDATE_MANDATE'][$_position]['success'];
            $this->position = $this->cache['R_REQ_UPDATE_MANDATE'][$_position]['position'];
            $this->value = $this->cache['R_REQ_UPDATE_MANDATE'][$_position]['value'];

            return $_success;
        }

        $_value26 = array();

        if (substr($this->string, $this->position, strlen('05')) === '05') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('05'));
            $this->position += strlen('05');
        } else {
            $_success = false;

            $this->report($this->position, '\'05\'');
        }

        if ($_success) {
            $_value26[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $oldPayeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value26[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $oldPayerNr = $this->value;
            }
        }

        if ($_success) {
            $_value26[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $newPayeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value26[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $newPayerNr = $this->value;
            }
        }

        if ($_success) {
            $_value26[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value26[] = $this->value;

            $this->value = $_value26;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$oldPayeeBg, &$oldPayerNr, &$newPayeeBg, &$newPayerNr, &$void) {
                return new Request\UpdateMandateRequestNode($this->lineNr, $oldPayeeBg, $oldPayerNr, $newPayeeBg, $newPayerNr, $void);
            });
        }

        $this->cache['R_REQ_UPDATE_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_REQ_UPDATE_MANDATE');
        }

        return $_success;
    }

    protected function parseR_REQ_DEL_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['R_REQ_DEL_MANDATE'][$_position])) {
            $_success = $this->cache['R_REQ_DEL_MANDATE'][$_position]['success'];
            $this->position = $this->cache['R_REQ_DEL_MANDATE'][$_position]['position'];
            $this->value = $this->cache['R_REQ_DEL_MANDATE'][$_position]['value'];

            return $_success;
        }

        $_value27 = array();

        if (substr($this->string, $this->position, strlen('03')) === '03') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('03'));
            $this->position += strlen('03');
        } else {
            $_success = false;

            $this->report($this->position, '\'03\'');
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value27[] = $this->value;

            $this->value = $_value27;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$payeeBg, &$payerNr, &$void) {
                return new Request\DeleteMandateRequestNode($this->lineNr, $payeeBg, $payerNr, $void);
            });
        }

        $this->cache['R_REQ_DEL_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_REQ_DEL_MANDATE');
        }

        return $_success;
    }

    protected function parseL_REQ_PAYMENT()
    {
        $_position = $this->position;

        if (isset($this->cache['L_REQ_PAYMENT'][$_position])) {
            $_success = $this->cache['L_REQ_PAYMENT'][$_position]['success'];
            $this->position = $this->cache['L_REQ_PAYMENT'][$_position]['position'];
            $this->value = $this->cache['L_REQ_PAYMENT'][$_position]['value'];

            return $_success;
        }

        $_value31 = array();

        $_success = $this->parseR_REQ_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value31[] = $this->value;

            $_success = $this->parseR_REQ_TRANSACTION();

            if ($_success) {
                $_value29 = array($this->value);
                $_cut30 = $this->cut;

                while (true) {
                    $_position28 = $this->position;

                    $this->cut = false;
                    $_success = $this->parseR_REQ_TRANSACTION();

                    if (!$_success) {
                        break;
                    }

                    $_value29[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position28;
                    $this->value = $_value29;
                }

                $this->cut = $_cut30;
            }

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value31[] = $this->value;

            $this->value = $_value31;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                return (new LayoutNode($open, ...$records))->setAttribute('layout_name', Layouts::LAYOUT_PAYMENT_REQUEST);
            });
        }

        $this->cache['L_REQ_PAYMENT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'L_REQ_PAYMENT');
        }

        return $_success;
    }

    protected function parseR_REQ_TRANSACTION()
    {
        $_position = $this->position;

        if (isset($this->cache['R_REQ_TRANSACTION'][$_position])) {
            $_success = $this->cache['R_REQ_TRANSACTION'][$_position]['success'];
            $this->position = $this->cache['R_REQ_TRANSACTION'][$_position]['position'];
            $this->value = $this->cache['R_REQ_TRANSACTION'][$_position]['value'];

            return $_success;
        }

        $_value36 = array();

        $_position32 = $this->position;
        $_cut33 = $this->cut;

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
            $this->position = $_position32;

            if (substr($this->string, $this->position, strlen('32')) === '32') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('32'));
                $this->position += strlen('32');
            } else {
                $_success = false;

                $this->report($this->position, '\'32\'');
            }
        }

        $this->cut = $_cut33;

        if ($_success) {
            $tc = $this->value;
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_position34 = $this->position;
            $_cut35 = $this->cut;

            $this->cut = false;
            $_success = $this->parseIMMEDIATE_DATE();

            if (!$_success && !$this->cut) {
                $this->position = $_position34;

                $_success = $this->parseDATE();
            }

            $this->cut = $_cut35;

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseINTERVAL();

            if ($_success) {
                $ival = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseREPS();

            if ($_success) {
                $reps = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseVOID();

            if ($_success) {
                $space = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseAMOUNT();

            if ($_success) {
                $amount = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseVARIABLE_TEXT();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $this->value = $_value36;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$tc, &$date, &$ival, &$reps, &$space, &$payerNr, &$amount, &$payeeBg, &$ref, &$void) {
                return $tc == '82'
                    ? new Request\IncomingTransactionRequestNode($this->lineNr, $date, $ival, $reps, $space, $payerNr, $amount, $payeeBg, $ref, $void)
                    : new Request\OutgoingTransactionRequestNode($this->lineNr, $date, $ival, $reps, $space, $payerNr, $amount, $payeeBg, $ref, $void);
            });
        }

        $this->cache['R_REQ_TRANSACTION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_REQ_TRANSACTION');
        }

        return $_success;
    }

    protected function parseL_REQ_AMENDMENT()
    {
        $_position = $this->position;

        if (isset($this->cache['L_REQ_AMENDMENT'][$_position])) {
            $_success = $this->cache['L_REQ_AMENDMENT'][$_position]['success'];
            $this->position = $this->cache['L_REQ_AMENDMENT'][$_position]['position'];
            $this->value = $this->cache['L_REQ_AMENDMENT'][$_position]['value'];

            return $_success;
        }

        $_value40 = array();

        $_success = $this->parseR_REQ_OPENING();

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value40[] = $this->value;

            if (substr($this->string, $this->position, strlen('TODO')) === 'TODO') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('TODO'));
                $this->position += strlen('TODO');
            } else {
                $_success = false;

                $this->report($this->position, '\'TODO\'');
            }

            if ($_success) {
                $_value38 = array($this->value);
                $_cut39 = $this->cut;

                while (true) {
                    $_position37 = $this->position;

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

                    $_value38[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position37;
                    $this->value = $_value38;
                }

                $this->cut = $_cut39;
            }

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value40[] = $this->value;

            $this->value = $_value40;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$records) {
                // TODO this is just a stub...
                return (new LayoutNode($open, ...$records))->setAttribute('layout_name', Layouts::LAYOUT_AMENDMENT_REQUEST);
            });
        }

        $this->cache['L_REQ_AMENDMENT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'L_REQ_AMENDMENT');
        }

        return $_success;
    }

    protected function parseL_RESP_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['L_RESP_MANDATE'][$_position])) {
            $_success = $this->cache['L_RESP_MANDATE'][$_position]['success'];
            $this->position = $this->cache['L_RESP_MANDATE'][$_position]['position'];
            $this->value = $this->cache['L_RESP_MANDATE'][$_position]['value'];

            return $_success;
        }

        $_value46 = array();

        $_position41 = $this->position;
        $_cut42 = $this->cut;

        $this->cut = false;
        $_success = $this->parseR_GENERIC_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position41;

            $_success = $this->parseR_RESP_MANDATE_OPENING_OLD();
        }

        $this->cut = $_cut42;

        if ($_success) {
            $open = $this->value;
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_value44 = array();
            $_cut45 = $this->cut;

            while (true) {
                $_position43 = $this->position;

                $this->cut = false;
                $_success = $this->parseR_RESP_MANDATE();

                if (!$_success) {
                    break;
                }

                $_value44[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position43;
                $this->value = $_value44;
            }

            $this->cut = $_cut45;

            if ($_success) {
                $mands = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseR_RESP_MANDATE_CLOSING();

            if ($_success) {
                $close = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $this->value = $_value46;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$open, &$mands, &$close) {
                return new FileNode(
                    (new LayoutNode($open, $close, ...$mands))->setAttribute('layout_name', Layouts::LAYOUT_MANDATE_RESPONSE)
                );
            });
        }

        $this->cache['L_RESP_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'L_RESP_MANDATE');
        }

        return $_success;
    }

    protected function parseR_RESP_MANDATE_OPENING_OLD()
    {
        $_position = $this->position;

        if (isset($this->cache['R_RESP_MANDATE_OPENING_OLD'][$_position])) {
            $_success = $this->cache['R_RESP_MANDATE_OPENING_OLD'][$_position]['success'];
            $this->position = $this->cache['R_RESP_MANDATE_OPENING_OLD'][$_position]['position'];
            $this->value = $this->cache['R_RESP_MANDATE_OPENING_OLD'][$_position]['value'];

            return $_success;
        }

        $_value49 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseBGC_CLEARING();

            if ($_success) {
                $clear = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_position48 = $this->position;

            $_value47 = array();

            $_success = $this->parseA5();

            if ($_success) {
                $_value47[] = $this->value;

                $_success = $this->parseA2();
            }

            if ($_success) {
                $_value47[] = $this->value;

                $_success = $this->parseA2();
            }

            if ($_success) {
                $_value47[] = $this->value;

                $this->value = $_value47;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position48, $this->position - $_position48));
            }

            if ($_success) {
                $layout = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value49[] = $this->value;

            $this->value = $_value49;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$clear, &$payeeBg, &$layout, &$void) {
                return new OpeningRecordNode(
                    $this->lineNr,
                    new TextNode($this->lineNr, ''),
                    new TextNode($this->lineNr, ''),
                    $date,
                    $clear,
                    new TextNode($this->lineNr, $layout, '/^AG-MEDAVI$/'),
                    new PayeeBgcNumberNode($this->lineNr, ''),
                    $payeeBg,
                    $void
                );
            });
        }

        $this->cache['R_RESP_MANDATE_OPENING_OLD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_RESP_MANDATE_OPENING_OLD');
        }

        return $_success;
    }

    protected function parseR_RESP_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['R_RESP_MANDATE'][$_position])) {
            $_success = $this->cache['R_RESP_MANDATE'][$_position]['success'];
            $this->position = $this->cache['R_RESP_MANDATE'][$_position]['position'];
            $this->value = $this->cache['R_RESP_MANDATE'][$_position]['value'];

            return $_success;
        }

        $_value60 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parsePAYEE_BG();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parseACCOUNT();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_position51 = $this->position;

            $_value50 = array();

            $_success = $this->parseA();

            if ($_success) {
                $_value50[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value50[] = $this->value;

                $this->value = $_value50;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position51, $this->position - $_position51));
            }

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_position53 = $this->position;

            $_value52 = array();

            $_success = $this->parseA();

            if ($_success) {
                $_value52[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value52[] = $this->value;

                $this->value = $_value52;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position53, $this->position - $_position53));
            }

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_position54 = $this->position;
            $_cut55 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position54;
                $this->value = null;
            }

            $this->cut = $_cut55;

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_position59 = $this->position;

            $_position57 = $this->position;
            $_cut58 = $this->cut;

            $this->cut = false;
            $_value56 = array();

            $_success = $this->parseA5();

            if ($_success) {
                $_value56[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value56[] = $this->value;

                $this->value = $_value56;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position57;
                $this->value = null;
            }

            $this->cut = $_cut58;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position59, $this->position - $_position59));
            }

            if ($_success) {
                $validDate = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $this->value = $_value60;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$payeeBg, &$payerNr, &$account, &$id, &$info, &$comment, &$date, &$validDate, &$void) {
                $account = $account->getValue()
                    ? $account
                    : new AccountNode($this->lineNr, $payerNr->getValue());

                // TODO här oven finns en A5 som måste sparas...

                // TODO $validDate måste sparas, se s 78 i specen, det är datum då medgivandet är giltigt för debitering..
                    // dock bara sex siffror, så det är inte riktigt som det vanliga date...

                return new Response\MandateResponseNode(
                    $this->lineNr,
                    $payeeBg,
                    $payerNr,
                    $account,
                    $id,
                    new MessageNode($this->lineNr, "73.$info"),
                    new MessageNode($this->lineNr, "73.comment.$comment"),
                    $date ?: new DateNode($this->lineNr, '@0'),
                    $void
                );
            });
        }

        $this->cache['R_RESP_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_RESP_MANDATE');
        }

        return $_success;
    }

    protected function parseR_RESP_MANDATE_CLOSING()
    {
        $_position = $this->position;

        if (isset($this->cache['R_RESP_MANDATE_CLOSING'][$_position])) {
            $_success = $this->cache['R_RESP_MANDATE_CLOSING'][$_position]['success'];
            $this->position = $this->cache['R_RESP_MANDATE_CLOSING'][$_position]['position'];
            $this->value = $this->cache['R_RESP_MANDATE_CLOSING'][$_position]['value'];

            return $_success;
        }

        $_value61 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
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

            $_success = $this->parseBGC_CLEARING();

            if ($_success) {
                $clear = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseINT7();

            if ($_success) {
                $nrOfPosts = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $void = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $this->value = $_value61;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$clear, &$nrOfPosts, &$void) {
                // TODO $clear måste sparas så att det kan valideras...
                return new ClosingRecordNode($this->lineNr, $date, $nrOfPosts, $void);
            });
        }

        $this->cache['R_RESP_MANDATE_CLOSING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_RESP_MANDATE_CLOSING');
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

        $_position63 = $this->position;

        $_value62 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value62[] = $this->value;

            $this->value = $_value62;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position63, $this->position - $_position63));
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

        $_position67 = $this->position;

        $_value66 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value66[] = $this->value;

            $this->value = $_value66;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position67, $this->position - $_position67));
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

    protected function parseAUTOGIRO()
    {
        $_position = $this->position;

        if (isset($this->cache['AUTOGIRO'][$_position])) {
            $_success = $this->cache['AUTOGIRO'][$_position]['success'];
            $this->position = $this->cache['AUTOGIRO'][$_position]['position'];
            $this->value = $this->cache['AUTOGIRO'][$_position]['value'];

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
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text, '/^AUTOGIRO$/');
            });
        }

        $this->cache['AUTOGIRO'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AUTOGIRO');
        }

        return $_success;
    }

    protected function parseBGC_CLEARING()
    {
        $_position = $this->position;

        if (isset($this->cache['BGC_CLEARING'][$_position])) {
            $_success = $this->cache['BGC_CLEARING'][$_position]['success'];
            $this->position = $this->cache['BGC_CLEARING'][$_position]['position'];
            $this->value = $this->cache['BGC_CLEARING'][$_position]['value'];

            return $_success;
        }

        $_position71 = $this->position;

        $_value70 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $this->value = $_value70;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position71, $this->position - $_position71));
        }

        if ($_success) {
            $clear = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$clear) {
                return new TextNode($this->lineNr, $clear, '/^9900$/');
            });
        }

        $this->cache['BGC_CLEARING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'BGC_CLEARING');
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

        $_position73 = $this->position;

        $_value72 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value72[] = $this->value;

            $this->value = $_value72;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position73, $this->position - $_position73));
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

        $_position75 = $this->position;

        $_value74 = array();

        $_success = $this->parseA5();

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

            $this->value = $_value74;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position75, $this->position - $_position75));
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

        $_position76 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position76, $this->position - $_position76));
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

    protected function parsePAYER_NR()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYER_NR'][$_position])) {
            $_success = $this->cache['PAYER_NR'][$_position]['success'];
            $this->position = $this->cache['PAYER_NR'][$_position]['position'];
            $this->value = $this->cache['PAYER_NR'][$_position]['value'];

            return $_success;
        }

        $_position78 = $this->position;

        $_value77 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value77[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value77[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value77[] = $this->value;

            $this->value = $_value77;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position78, $this->position - $_position78));
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

        $_position80 = $this->position;

        $_value79 = array();

        $_success = $this->parseA2();

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $this->value = $_value79;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position80, $this->position - $_position80));
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

        $_position82 = $this->position;

        $_value81 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value81[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value81[] = $this->value;

            $this->value = $_value81;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position82, $this->position - $_position82));
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

        $_position86 = $this->position;

        $_value84 = array();
        $_cut85 = $this->cut;

        while (true) {
            $_position83 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

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
            $this->value = strval(substr($this->string, $_position86, $this->position - $_position86));
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

    protected function parseTEXT20()
    {
        $_position = $this->position;

        if (isset($this->cache['TEXT20'][$_position])) {
            $_success = $this->cache['TEXT20'][$_position]['success'];
            $this->position = $this->cache['TEXT20'][$_position]['position'];
            $this->value = $this->cache['TEXT20'][$_position]['value'];

            return $_success;
        }

        $_position88 = $this->position;

        $_value87 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value87[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value87[] = $this->value;

            $this->value = $_value87;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position88, $this->position - $_position88));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text);
            });
        }

        $this->cache['TEXT20'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'TEXT20');
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

        $_position89 = $this->position;

        $_success = $this->parseA();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position89, $this->position - $_position89));
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

    protected function parseVOID10()
    {
        $_position = $this->position;

        if (isset($this->cache['VOID10'][$_position])) {
            $_success = $this->cache['VOID10'][$_position]['success'];
            $this->position = $this->cache['VOID10'][$_position]['position'];
            $this->value = $this->cache['VOID10'][$_position]['value'];

            return $_success;
        }

        $_position90 = $this->position;

        $_success = $this->parseA10();

        if ($_success) {
            $this->value = strval(substr($this->string, $_position90, $this->position - $_position90));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text, '/^ {10}$/');
            });
        }

        $this->cache['VOID10'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'VOID10');
        }

        return $_success;
    }

    protected function parseVOID12()
    {
        $_position = $this->position;

        if (isset($this->cache['VOID12'][$_position])) {
            $_success = $this->cache['VOID12'][$_position]['success'];
            $this->position = $this->cache['VOID12'][$_position]['position'];
            $this->value = $this->cache['VOID12'][$_position]['value'];

            return $_success;
        }

        $_position92 = $this->position;

        $_value91 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value91[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value91[] = $this->value;

            $this->value = $_value91;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position92, $this->position - $_position92));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text, '/^ {12}$/');
            });
        }

        $this->cache['VOID12'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'VOID12');
        }

        return $_success;
    }

    protected function parseVOID14()
    {
        $_position = $this->position;

        if (isset($this->cache['VOID14'][$_position])) {
            $_success = $this->cache['VOID14'][$_position]['success'];
            $this->position = $this->cache['VOID14'][$_position]['position'];
            $this->value = $this->cache['VOID14'][$_position]['value'];

            return $_success;
        }

        $_position94 = $this->position;

        $_value93 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value93[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value93[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value93[] = $this->value;

            $this->value = $_value93;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position94, $this->position - $_position94));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text, '/^ {14}$/');
            });
        }

        $this->cache['VOID14'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'VOID14');
        }

        return $_success;
    }

    protected function parseVOID20()
    {
        $_position = $this->position;

        if (isset($this->cache['VOID20'][$_position])) {
            $_success = $this->cache['VOID20'][$_position]['success'];
            $this->position = $this->cache['VOID20'][$_position]['position'];
            $this->value = $this->cache['VOID20'][$_position]['value'];

            return $_success;
        }

        $_position96 = $this->position;

        $_value95 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value95[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value95[] = $this->value;

            $this->value = $_value95;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position96, $this->position - $_position96));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text, '/^ {20}$/');
            });
        }

        $this->cache['VOID20'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'VOID20');
        }

        return $_success;
    }

    protected function parseVOID44()
    {
        $_position = $this->position;

        if (isset($this->cache['VOID44'][$_position])) {
            $_success = $this->cache['VOID44'][$_position]['success'];
            $this->position = $this->cache['VOID44'][$_position]['position'];
            $this->value = $this->cache['VOID44'][$_position]['value'];

            return $_success;
        }

        $_position98 = $this->position;

        $_value97 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value97[] = $this->value;

            $_success = $this->parseA2();
        }

        if ($_success) {
            $_value97[] = $this->value;

            $this->value = $_value97;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position98, $this->position - $_position98));
        }

        if ($_success) {
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->lineNr + 1, $text, '/^ {44}$/');
            });
        }

        $this->cache['VOID44'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'VOID44');
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

        $_position100 = $this->position;

        $_value99 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value99[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value99[] = $this->value;

            $this->value = $_value99;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position100, $this->position - $_position100));
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

        $_position102 = $this->position;

        $_value101 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_success = $this->parseA();
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

        $_position104 = $this->position;

        $_value103 = array();

        $_success = $this->parseA5();

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

    protected function parseEOR()
    {
        $_position = $this->position;

        if (isset($this->cache['EOR'][$_position])) {
            $_success = $this->cache['EOR'][$_position]['success'];
            $this->position = $this->cache['EOR'][$_position]['position'];
            $this->value = $this->cache['EOR'][$_position]['value'];

            return $_success;
        }

        $_value110 = array();

        $_value106 = array();
        $_cut107 = $this->cut;

        while (true) {
            $_position105 = $this->position;

            $this->cut = false;
            $_success = $this->parseVOID();

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
            $void = $this->value;
        }

        if ($_success) {
            $_value110[] = $this->value;

            $_position108 = $this->position;
            $_cut109 = $this->cut;

            $this->cut = false;
            $_success = $this->parseEOL();

            if (!$_success && !$this->cut) {
                $this->position = $_position108;

                $_success = $this->parseEOF();
            }

            $this->cut = $_cut109;
        }

        if ($_success) {
            $_value110[] = $this->value;

            $this->value = $_value110;
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

        $_value113 = array();

        $_position111 = $this->position;
        $_cut112 = $this->cut;

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
            $this->position = $_position111;
            $this->value = null;
        }

        $this->cut = $_cut112;

        if ($_success) {
            $_value113[] = $this->value;

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
            $_value113[] = $this->value;

            $this->value = $_value113;
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

        $_position114 = $this->position;
        $_cut115 = $this->cut;

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

        $this->position = $_position114;
        $this->cut = $_cut115;

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