<?php

namespace byrokrat\autogiro;

use byrokrat\autogiro\Exception\ParserException;
use byrokrat\autogiro\Tree\Account;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\BgcCustomerNumberNode;
use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\Date;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\Id;
use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\Record\Request;
use byrokrat\autogiro\Tree\Record\Response;
use byrokrat\autogiro\Tree\Repeats;
use byrokrat\autogiro\Tree\SpaceNode;
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
            $_success = $this->parseL_REQ_CONTAINER();

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
                $this->currentLineNr = 0;
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
            $_value4[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseS();
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

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseA20();

            if ($_success) {
                $layout = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseCUST_NR();

            if ($_success) {
                $custNr = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value4[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value4[] = $this->value;

            $this->value = $_value4;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$layout, &$custNr, &$bg) {
                return new OpeningNode($this->currentLineNr, rtrim($layout), $date, $custNr, $bg);
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

    protected function parseL_REQ_CONTAINER()
    {
        $_position = $this->position;

        if (isset($this->cache['L_REQ_CONTAINER'][$_position])) {
            $_success = $this->cache['L_REQ_CONTAINER'][$_position]['success'];
            $this->position = $this->cache['L_REQ_CONTAINER'][$_position]['position'];
            $this->value = $this->cache['L_REQ_CONTAINER'][$_position]['value'];

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

        $this->cache['L_REQ_CONTAINER'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'L_REQ_CONTAINER');
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
            $_value10[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseS20();
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseCUST_NR();

            if ($_success) {
                $custNr = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value10[] = $this->value;

            $this->value = $_value10;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$custNr, &$bg) {
                return new OpeningNode($this->currentLineNr, '', $date, $custNr, $bg);
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
            $opening = $this->value;
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
            $this->value = call_user_func(function () use (&$opening, &$records) {
                return new LayoutNode(
                    $opening->setAttribute('layout_name', Layouts::LAYOUT_MANDATE_REQUEST),
                    new ClosingNode($this->currentLineNr, $opening->getChild('date'), count($records)),
                    ...$records
                );
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

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
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
            $_success = $this->parseS20();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position21;
                $this->value = null;
            }

            $this->cut = $_cut22;
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
        }

        if ($_success) {
            $_value25[] = $this->value;

            $this->value = $_value25;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$account, &$id, &$reject) {
                if ($reject == 'AV') {
                    return new Request\RejectMandateRequestNode($this->currentLineNr, $bg, $payerNr);
                }

                return $account && $id
                    ? new Request\CreateMandateRequestNode($this->currentLineNr, $bg, $payerNr, $account, $id)
                    : new Request\AcceptMandateRequestNode($this->currentLineNr, $bg, $payerNr);
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

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $oldBg = $this->value;
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

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $newBg = $this->value;
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
        }

        if ($_success) {
            $_value26[] = $this->value;

            $this->value = $_value26;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$oldBg, &$oldPayerNr, &$newBg, &$newPayerNr) {
                return new Request\UpdateMandateRequestNode($this->currentLineNr, $oldBg, $oldPayerNr, $newBg, $newPayerNr);
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

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
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
        }

        if ($_success) {
            $_value27[] = $this->value;

            $this->value = $_value27;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr) {
                return new Request\DeleteMandateRequestNode($this->currentLineNr, $bg, $payerNr);
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
            $opening = $this->value;
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
            $this->value = call_user_func(function () use (&$opening, &$records) {
                return new LayoutNode(
                    $opening->setAttribute('layout_name', Layouts::LAYOUT_PAYMENT_REQUEST),
                    new ClosingNode($this->currentLineNr, $opening->getChild('date'), count($records)),
                    ...$records
                );
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
                $interval = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseREPEATS();

            if ($_success) {
                $repeats = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseVOID();

            if ($_success) {
                $void = $this->value;
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

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $payeeBg = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseTEXT16();

            if ($_success) {
                $ref = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseEOR();

            if ($_success) {
                $end = $this->value;
            }
        }

        if ($_success) {
            $_value36[] = $this->value;

            $this->value = $_value36;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$tc, &$date, &$interval, &$repeats, &$void, &$payerNr, &$amount, &$payeeBg, &$ref, &$end) {
                // TODO processor som kontrollerar att periodkod inte används vid GENAST (se dokument)
                // TODO processor som kontrollerar att interval=0 paras med no repeats (se dokument)

                // TODO fortsätt med nästa argument...
                    // nästa är betalningsmottagarens bankgironummer, passa på att göra överallt:
                        //Där bankgiro är payee borde jag byta namn på attribute till payee_bankgiro !!
                        //payee_bgc_customer_nr ??

                // TODO SpaceNode krävs för att vi ska kunna skriva ut mellanslag...      [KLAR]
                // TODO processor som kontrollerar att SpaceNode innehåller ett space...

                // TODO ska jag skapa abstract Tree\RecordNode? Att använda exempelvis i LayoutNode...
                    // Ja, kan även implementera ett visst antal space på slutet på något generellt sätt...
                    // EOR kan returnera alla space den hittar i slutet av sträng... (Har jag börjat pilla på...)


                // DESSA 2 kan jag göra först, enkla och påverkar inte någonting annat...
                // TODO flytta alla Request nodes till Tree\Request
                    // passa på att byta namn på alla också...
                    // gäller även response...
                    // passa även på att fixa så att alla ärver RecordNode.....

                return $tc == '82'
                    ? new Request\IncomingTransactionRequestNode($this->currentLineNr, $date, $interval, $repeats, $void, $payerNr, $amount, $payeeBg, $ref, $end)
                    : new Request\OutgoingTransactionRequestNode($this->currentLineNr, $date, $interval, $repeats, $void, $payerNr, $amount, $payeeBg, $ref, $end);
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
            $opening = $this->value;
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
            $this->value = call_user_func(function () use (&$opening, &$records) {
                // TODO this is just a stub...
                return new LayoutNode(
                    $opening->setAttribute('layout_name', Layouts::LAYOUT_AMENDMENT_REQUEST),
                    new ClosingNode($this->currentLineNr, $opening->getChild('date'), count($records)),
                    ...$records
                );
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
            $opening = $this->value;
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
                $mandates = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseR_RESP_MANDATE_CLOSING();

            if ($_success) {
                $closing = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $this->value = $_value46;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$opening, &$mandates, &$closing) {
                return new FileNode(new LayoutNode($opening, $closing, ...$mandates));
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

        $_value47 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value47[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value47[] = $this->value;

            $_success = $this->parseBGC_CLEARING();
        }

        if ($_success) {
            $_value47[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value47[] = $this->value;

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
            $_value47[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value47[] = $this->value;

            $this->value = $_value47;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bg) {
                return new OpeningNode(
                    $this->currentLineNr,
                    'AG-MEDAVI',
                    $date,
                    new BgcCustomerNumberNode($this->currentLineNr, ''),
                    $bg
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

        $_value54 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseACCOUNT();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_position49 = $this->position;

            $_value48 = array();

            $_success = $this->parseN();

            if ($_success) {
                $_value48[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value48[] = $this->value;

                $this->value = $_value48;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position49, $this->position - $_position49));
            }

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_position51 = $this->position;

            $_value50 = array();

            $_success = $this->parseN();

            if ($_success) {
                $_value50[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value50[] = $this->value;

                $this->value = $_value50;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position51, $this->position - $_position51));
            }

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_position52 = $this->position;
            $_cut53 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position52;
                $this->value = null;
            }

            $this->cut = $_cut53;

            if ($_success) {
                $date = $this->value;
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
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$account, &$id, &$info, &$comment, &$date) {
                $account = $account->getValue()
                    ? $account
                    : new Account\AccountNode($this->currentLineNr, $payerNr->getValue());

                return new Response\MandateResponseNode(
                    $this->currentLineNr,
                    $bg,
                    $payerNr,
                    $account,
                    $id,
                    new MessageNode($this->currentLineNr, "73.$info"),
                    new MessageNode($this->currentLineNr, "73.comment.$comment"),
                    $date ?: new Date\DateNode($this->currentLineNr, '@0')
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

        $_value57 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
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

            $_success = $this->parseBGC_CLEARING();
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_position56 = $this->position;

            $_value55 = array();

            $_success = $this->parseN5();

            if ($_success) {
                $_value55[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value55[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value55[] = $this->value;

                $this->value = $_value55;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position56, $this->position - $_position56));
            }

            if ($_success) {
                $nrOfPosts = $this->value;
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
            $this->value = call_user_func(function () use (&$date, &$nrOfPosts) {
                return new ClosingNode($this->currentLineNr, $date, intval($nrOfPosts));
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

        $_position59 = $this->position;

        $_value58 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $this->value = $_value58;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position59, $this->position - $_position59));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new Account\AccountNode($this->currentLineNr, trim(ltrim($number, '0')));
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

        $_position61 = $this->position;

        $_value60 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parseA();
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
            $amount = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$amount) {
                return new AmountNode($this->currentLineNr, trim(ltrim($amount, '0')));
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
                return new Account\BankgiroNode($this->currentLineNr, trim(ltrim($number, '0')));
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

    protected function parseBGC_CLEARING()
    {
        $_position = $this->position;

        if (isset($this->cache['BGC_CLEARING'][$_position])) {
            $_success = $this->cache['BGC_CLEARING'][$_position]['success'];
            $this->position = $this->cache['BGC_CLEARING'][$_position]['position'];
            $this->value = $this->cache['BGC_CLEARING'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen('9900')) === '9900') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('9900'));
            $this->position += strlen('9900');
        } else {
            $_success = false;

            $this->report($this->position, '\'9900\'');
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

    protected function parseCUST_NR()
    {
        $_position = $this->position;

        if (isset($this->cache['CUST_NR'][$_position])) {
            $_success = $this->cache['CUST_NR'][$_position]['success'];
            $this->position = $this->cache['CUST_NR'][$_position]['position'];
            $this->value = $this->cache['CUST_NR'][$_position]['value'];

            return $_success;
        }

        $_position63 = $this->position;

        $_value62 = array();

        $_success = $this->parseA5();

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
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new BgcCustomerNumberNode($this->currentLineNr, trim(ltrim($nr, '0')));
            });
        }

        $this->cache['CUST_NR'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'CUST_NR');
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

        $_position65 = $this->position;

        $_value64 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $this->value = $_value64;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position65, $this->position - $_position65));
        }

        if ($_success) {
            $date = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date) {
                return new Date\DateNode($this->currentLineNr, trim($date));
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

        $_value66 = array();

        if (substr($this->string, $this->position, strlen('GENAST')) === 'GENAST') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('GENAST'));
            $this->position += strlen('GENAST');
        } else {
            $_success = false;

            $this->report($this->position, '\'GENAST\'');
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value66[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value66[] = $this->value;

            $this->value = $_value66;
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return new Date\ImmediateDateNode($this->currentLineNr);
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

    protected function parseID()
    {
        $_position = $this->position;

        if (isset($this->cache['ID'][$_position])) {
            $_success = $this->cache['ID'][$_position]['success'];
            $this->position = $this->cache['ID'][$_position]['position'];
            $this->value = $this->cache['ID'][$_position]['value'];

            return $_success;
        }

        $_value69 = array();

        $_position68 = $this->position;

        $_value67 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value67[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value67[] = $this->value;

            $this->value = $_value67;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position68, $this->position - $_position68));
        }

        if ($_success) {
            $century = $this->value;
        }

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseA10();

            if ($_success) {
                $number = $this->value;
            }
        }

        if ($_success) {
            $_value69[] = $this->value;

            $this->value = $_value69;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$century, &$number) {
                return in_array($century, ['00', '99'])
                    ? new Id\OrganizationIdNode($this->currentLineNr, trim($number))
                    : new Id\PersonalIdNode($this->currentLineNr, trim($century.$number));
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

    protected function parseINTERVAL()
    {
        $_position = $this->position;

        if (isset($this->cache['INTERVAL'][$_position])) {
            $_success = $this->cache['INTERVAL'][$_position]['success'];
            $this->position = $this->cache['INTERVAL'][$_position]['position'];
            $this->value = $this->cache['INTERVAL'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseA();

        if ($_success) {
            $interval = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$interval) {
                switch ($interval) {
                    case '0': return new Interval\IntervalOnceNode;
                    case '1': return new Interval\IntervalMonthlyOnDateNode;
                    case '2': return new Interval\IntervalQuarterlyOnDateNode;
                    case '3': return new Interval\IntervalSemiannuallyOnDateNode;
                    case '4': return new Interval\IntervalAnnuallyOnDateNode;
                    case '5': return new Interval\IntervalMonthlyOnLastCalendarDayNode;
                    case '6': return new Interval\IntervalQuarterlyOnLastCalendarDayNode;
                    case '7': return new Interval\IntervalSemiannuallyOnLastCalendarDayNode;
                    case '8': return new Interval\IntervalAnnuallyOnLastCalendarDayNode;
                    default: throw new ParserException("Invalid interval identifier $interval");
                }
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

        $_position71 = $this->position;

        $_value70 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value70[] = $this->value;

            $this->value = $_value70;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position71, $this->position - $_position71));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new PayerNumberNode($this->currentLineNr, trim(ltrim($nr, '0')));
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

    protected function parseREPEATS()
    {
        $_position = $this->position;

        if (isset($this->cache['REPEATS'][$_position])) {
            $_success = $this->cache['REPEATS'][$_position]['success'];
            $this->position = $this->cache['REPEATS'][$_position]['position'];
            $this->value = $this->cache['REPEATS'][$_position]['value'];

            return $_success;
        }

        $_position73 = $this->position;

        $_value72 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value72[] = $this->value;

            $_success = $this->parseA();
        }

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
            $repeats = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$repeats) {
                $repeats = trim(ltrim($repeats, '0'));

                if ($repeats) {
                    return new Repeats\RepeatsNode($this->currentLineNr, $repeats);
                }

                return new Repeats\RepeatsUnspecifiedNode($this->currentLineNr);
            });
        }

        $this->cache['REPEATS'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'REPEATS');
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

        if (preg_match('/^[a-zA-Z0-9 -\\/&]$/', substr($this->string, $this->position, 1))) {
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

    protected function parseA5()
    {
        $_position = $this->position;

        if (isset($this->cache['A5'][$_position])) {
            $_success = $this->cache['A5'][$_position]['success'];
            $this->position = $this->cache['A5'][$_position]['position'];
            $this->value = $this->cache['A5'][$_position]['value'];

            return $_success;
        }

        $_position75 = $this->position;

        $_value74 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value74[] = $this->value;

            $_success = $this->parseA();
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

        $_position77 = $this->position;

        $_value76 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value76[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value76[] = $this->value;

            $this->value = $_value76;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position77, $this->position - $_position77));
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

    protected function parseA20()
    {
        $_position = $this->position;

        if (isset($this->cache['A20'][$_position])) {
            $_success = $this->cache['A20'][$_position]['success'];
            $this->position = $this->cache['A20'][$_position]['position'];
            $this->value = $this->cache['A20'][$_position]['value'];

            return $_success;
        }

        $_position79 = $this->position;

        $_value78 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value78[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value78[] = $this->value;

            $this->value = $_value78;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position79, $this->position - $_position79));
        }

        $this->cache['A20'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'A20');
        }

        return $_success;
    }

    protected function parseN()
    {
        $_position = $this->position;

        if (isset($this->cache['N'][$_position])) {
            $_success = $this->cache['N'][$_position]['success'];
            $this->position = $this->cache['N'][$_position]['position'];
            $this->value = $this->cache['N'][$_position]['value'];

            return $_success;
        }

        if (preg_match('/^[0-9]$/', substr($this->string, $this->position, 1))) {
            $_success = true;
            $this->value = substr($this->string, $this->position, 1);
            $this->position += 1;
        } else {
            $_success = false;
        }

        $this->cache['N'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, "NUMBER");
        }

        return $_success;
    }

    protected function parseN5()
    {
        $_position = $this->position;

        if (isset($this->cache['N5'][$_position])) {
            $_success = $this->cache['N5'][$_position]['success'];
            $this->position = $this->cache['N5'][$_position]['position'];
            $this->value = $this->cache['N5'][$_position]['value'];

            return $_success;
        }

        $_position81 = $this->position;

        $_value80 = array();

        $_success = $this->parseN();

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value80[] = $this->value;

            $this->value = $_value80;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position81, $this->position - $_position81));
        }

        $this->cache['N5'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'N5');
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

    protected function parseS10()
    {
        $_position = $this->position;

        if (isset($this->cache['S10'][$_position])) {
            $_success = $this->cache['S10'][$_position]['success'];
            $this->position = $this->cache['S10'][$_position]['position'];
            $this->value = $this->cache['S10'][$_position]['value'];

            return $_success;
        }

        $_position83 = $this->position;

        $_value82 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value82[] = $this->value;

            $this->value = $_value82;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position83, $this->position - $_position83));
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

        $_position85 = $this->position;

        $_value84 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value84[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value84[] = $this->value;

            $this->value = $_value84;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position85, $this->position - $_position85));
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

    protected function parseVOID()
    {
        $_position = $this->position;

        if (isset($this->cache['VOID'][$_position])) {
            $_success = $this->cache['VOID'][$_position]['success'];
            $this->position = $this->cache['VOID'][$_position]['position'];
            $this->value = $this->cache['VOID'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseA();

        if ($_success) {
            $chars = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$chars) {
                return new SpaceNode($this->currentLineNr, $chars);
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

        $_success = $this->parseA10();

        if ($_success) {
            $chars = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$chars) {
                return new SpaceNode($this->currentLineNr, $chars);
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

    protected function parseVOID20()
    {
        $_position = $this->position;

        if (isset($this->cache['VOID20'][$_position])) {
            $_success = $this->cache['VOID20'][$_position]['success'];
            $this->position = $this->cache['VOID20'][$_position]['position'];
            $this->value = $this->cache['VOID20'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseA20();

        if ($_success) {
            $chars = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$chars) {
                return new SpaceNode($this->currentLineNr, $chars);
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

    protected function parseTEXT16()
    {
        $_position = $this->position;

        if (isset($this->cache['TEXT16'][$_position])) {
            $_success = $this->cache['TEXT16'][$_position]['success'];
            $this->position = $this->cache['TEXT16'][$_position]['position'];
            $this->value = $this->cache['TEXT16'][$_position]['value'];

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
            $text = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$text) {
                return new TextNode($this->currentLineNr, $text);
            });
        }

        $this->cache['TEXT16'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'TEXT16');
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

        $_value93 = array();

        $_value89 = array();
        $_cut90 = $this->cut;

        while (true) {
            $_position88 = $this->position;

            $this->cut = false;
            $_success = $this->parseVOID();

            if (!$_success) {
                break;
            }

            $_value89[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position88;
            $this->value = $_value89;
        }

        $this->cut = $_cut90;

        if ($_success) {
            $end = $this->value;
        }

        if ($_success) {
            $_value93[] = $this->value;

            $_position91 = $this->position;
            $_cut92 = $this->cut;

            $this->cut = false;
            $_success = $this->parseEOL();

            if (!$_success && !$this->cut) {
                $this->position = $_position91;

                $_success = $this->parseEOF();
            }

            $this->cut = $_cut92;
        }

        if ($_success) {
            $_value93[] = $this->value;

            $this->value = $_value93;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$end) {
                // TODO alla ställen där jag använder EOR ska dessa end voids fångas upp...
                return $end;
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

        $_value96 = array();

        $_position94 = $this->position;
        $_cut95 = $this->cut;

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
            $this->position = $_position94;
            $this->value = null;
        }

        $this->cut = $_cut95;

        if ($_success) {
            $_value96[] = $this->value;

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
            $_value96[] = $this->value;

            $this->value = $_value96;
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                $this->currentLineNr++;
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

        $_position97 = $this->position;
        $_cut98 = $this->cut;

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

        $this->position = $_position97;
        $this->cut = $_cut98;

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