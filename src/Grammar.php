<?php

namespace byrokrat\autogiro;

use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\RequestMandateCreationNode;
use byrokrat\autogiro\Tree\RequestMandateAcceptanceNode;
use byrokrat\autogiro\Tree\RequestMandateRejectionNode;
use byrokrat\autogiro\Tree\RequestMandateUpdateNode;
use byrokrat\autogiro\Tree\RequestMandateDeletionNode;
use byrokrat\autogiro\Tree\RequestIncomingTransactionNode;
use byrokrat\autogiro\Tree\RequestOutgoingTransactionNode;
use byrokrat\autogiro\Tree\RequestTransactionDeletionNode;
use byrokrat\autogiro\Tree\RequestTransactionUpdateNode;
use byrokrat\autogiro\Tree\MandateResponseNode;
use byrokrat\autogiro\Tree\PersonalIdNode;
use byrokrat\autogiro\Tree\OrganizationIdNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\BgcCustomerNumberNode;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\PayerNumberNode;

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
                return new OpeningNode(rtrim($layout), $date, $custNr, $bg, $this->currentLineNr);
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
                // TODO layout_request kan vara ''
                    // kanske lägga in ett test i visitor som kontrollerar att layoutId är ett känt värde... ??
                return new OpeningNode('', $date, $custNr, $bg, $this->currentLineNr);
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

                $_success = $this->parseR_REQ_CONFIRM_MANDATE();
            }

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

                        $_success = $this->parseR_REQ_CONFIRM_MANDATE();
                    }

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
                $opening->setAttribute('layout_name', Layouts::LAYOUT_MANDATE_REQUEST);

                return new LayoutNode(
                    $opening,
                    new ClosingNode($opening->getAttribute('date'), count($records), $this->currentLineNr),
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

        $_value20 = array();

        if (substr($this->string, $this->position, strlen('04')) === '04') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('04'));
            $this->position += strlen('04');
        } else {
            $_success = false;

            $this->report($this->position, '\'04\'');
        }

        if ($_success) {
            $_value20[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value20[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value20[] = $this->value;

            $_success = $this->parseACCOUNT();

            if ($_success) {
                $account = $this->value;
            }
        }

        if ($_success) {
            $_value20[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value20[] = $this->value;

            $_position18 = $this->position;
            $_cut19 = $this->cut;

            $this->cut = false;
            $_value17 = array();

            $_success = $this->parseS20();

            if ($_success) {
                $_value17[] = $this->value;

                $_success = $this->parseS();
            }

            if ($_success) {
                $_value17[] = $this->value;

                $_success = $this->parseS();
            }

            if ($_success) {
                $_value17[] = $this->value;

                $this->value = $_value17;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position18;
                $this->value = null;
            }

            $this->cut = $_cut19;
        }

        if ($_success) {
            $_value20[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value20[] = $this->value;

            $this->value = $_value20;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$account, &$id) {
                return new RequestMandateCreationNode($bg, $payerNr, $account, $id, $this->currentLineNr);
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

    protected function parseR_REQ_CONFIRM_MANDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['R_REQ_CONFIRM_MANDATE'][$_position])) {
            $_success = $this->cache['R_REQ_CONFIRM_MANDATE'][$_position]['success'];
            $this->position = $this->cache['R_REQ_CONFIRM_MANDATE'][$_position]['position'];
            $this->value = $this->cache['R_REQ_CONFIRM_MANDATE'][$_position]['value'];

            return $_success;
        }

        $_value24 = array();

        if (substr($this->string, $this->position, strlen('04')) === '04') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('04'));
            $this->position += strlen('04');
        } else {
            $_success = false;

            $this->report($this->position, '\'04\'');
        }

        if ($_success) {
            $_value24[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value24[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value24[] = $this->value;

            $_position22 = $this->position;
            $_cut23 = $this->cut;

            $this->cut = false;
            $_value21 = array();

            $_success = $this->parseA40();

            if ($_success) {
                $_value21[] = $this->value;

                $_success = $this->parseA5();
            }

            if ($_success) {
                $_value21[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value21[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value21[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value21[] = $this->value;

                if (substr($this->string, $this->position, strlen('AV')) === 'AV') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen('AV'));
                    $this->position += strlen('AV');
                } else {
                    $_success = false;

                    $this->report($this->position, '\'AV\'');
                }

                if ($_success) {
                    $reject = $this->value;
                }
            }

            if ($_success) {
                $_value21[] = $this->value;

                $this->value = $_value21;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position22;
                $this->value = null;
            }

            $this->cut = $_cut23;
        }

        if ($_success) {
            $_value24[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value24[] = $this->value;

            $this->value = $_value24;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$reject) {
                return $reject == 'AV'
                    ? new RequestMandateRejectionNode($bg, $payerNr, $this->currentLineNr)
                    : new RequestMandateAcceptanceNode($bg, $payerNr, $this->currentLineNr);
            });
        }

        $this->cache['R_REQ_CONFIRM_MANDATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'R_REQ_CONFIRM_MANDATE');
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

        $_value25 = array();

        if (substr($this->string, $this->position, strlen('05')) === '05') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('05'));
            $this->position += strlen('05');
        } else {
            $_success = false;

            $this->report($this->position, '\'05\'');
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $oldBg = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $oldPayerNr = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $newBg = $this->value;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $newPayerNr = $this->value;
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
            $this->value = call_user_func(function () use (&$oldBg, &$oldPayerNr, &$newBg, &$newPayerNr) {
                return new RequestMandateUpdateNode($oldBg, $oldPayerNr, $newBg, $newPayerNr, $this->currentLineNr);
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

        $_value26 = array();

        if (substr($this->string, $this->position, strlen('03')) === '03') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('03'));
            $this->position += strlen('03');
        } else {
            $_success = false;

            $this->report($this->position, '\'03\'');
        }

        if ($_success) {
            $_value26[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value26[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
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
            $this->value = call_user_func(function () use (&$bg, &$payerNr) {
                return new RequestMandateDeletionNode($bg, $payerNr, $this->currentLineNr);
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

        $_value30 = array();

        $_success = $this->parseR_REQ_OPENING();

        if ($_success) {
            $opening = $this->value;
        }

        if ($_success) {
            $_value30[] = $this->value;

            if (substr($this->string, $this->position, strlen('')) === '') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen(''));
                $this->position += strlen('');
            } else {
                $_success = false;

                $this->report($this->position, '\'\'');
            }

            if ($_success) {
                $_value28 = array($this->value);
                $_cut29 = $this->cut;

                while (true) {
                    $_position27 = $this->position;

                    $this->cut = false;
                    if (substr($this->string, $this->position, strlen('')) === '') {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, strlen(''));
                        $this->position += strlen('');
                    } else {
                        $_success = false;

                        $this->report($this->position, '\'\'');
                    }

                    if (!$_success) {
                        break;
                    }

                    $_value28[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position27;
                    $this->value = $_value28;
                }

                $this->cut = $_cut29;
            }

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value30[] = $this->value;

            $this->value = $_value30;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$opening, &$records) {
                $opening->setAttribute('layout_name', Layouts::LAYOUT_PAYMENT_REQUEST);

                return new LayoutNode(
                    $opening,
                    new ClosingNode($opening->getAttribute('date'), count($records), $this->currentLineNr),
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

    protected function parseL_REQ_AMENDMENT()
    {
        $_position = $this->position;

        if (isset($this->cache['L_REQ_AMENDMENT'][$_position])) {
            $_success = $this->cache['L_REQ_AMENDMENT'][$_position]['success'];
            $this->position = $this->cache['L_REQ_AMENDMENT'][$_position]['position'];
            $this->value = $this->cache['L_REQ_AMENDMENT'][$_position]['value'];

            return $_success;
        }

        $_value34 = array();

        $_success = $this->parseR_REQ_OPENING();

        if ($_success) {
            $opening = $this->value;
        }

        if ($_success) {
            $_value34[] = $this->value;

            if (substr($this->string, $this->position, strlen('')) === '') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen(''));
                $this->position += strlen('');
            } else {
                $_success = false;

                $this->report($this->position, '\'\'');
            }

            if ($_success) {
                $_value32 = array($this->value);
                $_cut33 = $this->cut;

                while (true) {
                    $_position31 = $this->position;

                    $this->cut = false;
                    if (substr($this->string, $this->position, strlen('')) === '') {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, strlen(''));
                        $this->position += strlen('');
                    } else {
                        $_success = false;

                        $this->report($this->position, '\'\'');
                    }

                    if (!$_success) {
                        break;
                    }

                    $_value32[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position31;
                    $this->value = $_value32;
                }

                $this->cut = $_cut33;
            }

            if ($_success) {
                $records = $this->value;
            }
        }

        if ($_success) {
            $_value34[] = $this->value;

            $this->value = $_value34;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$opening, &$records) {
                $opening->setAttribute('layout_name', Layouts::LAYOUT_AMENDMENT_REQUEST);

                return new LayoutNode(
                    $opening,
                    new ClosingNode($opening->getAttribute('date'), count($records), $this->currentLineNr),
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

        $_value40 = array();

        $_position35 = $this->position;
        $_cut36 = $this->cut;

        $this->cut = false;
        $_success = $this->parseR_GENERIC_OPENING();

        if (!$_success && !$this->cut) {
            $this->position = $_position35;

            $_success = $this->parseR_RESP_MANDATE_OPENING_OLD();
        }

        $this->cut = $_cut36;

        if ($_success) {
            $opening = $this->value;
        }

        if ($_success) {
            $_value40[] = $this->value;

            $_value38 = array();
            $_cut39 = $this->cut;

            while (true) {
                $_position37 = $this->position;

                $this->cut = false;
                $_success = $this->parseR_RESP_MANDATE();

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
                $mandates = $this->value;
            }
        }

        if ($_success) {
            $_value40[] = $this->value;

            $_success = $this->parseR_RESP_MANDATE_CLOSING();

            if ($_success) {
                $closing = $this->value;
            }
        }

        if ($_success) {
            $_value40[] = $this->value;

            $this->value = $_value40;
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

        $_value41 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value41[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value41[] = $this->value;

            $_success = $this->parseBGC_CLEARING();
        }

        if ($_success) {
            $_value41[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value41[] = $this->value;

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
            $_value41[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value41[] = $this->value;

            $this->value = $_value41;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bg) {
                return new OpeningNode(
                    'AG-MEDAVI',
                    $date,
                    new BgcCustomerNumberNode($this->currentLineNr, ''),
                    $bg,
                    $this->currentLineNr
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

        $_value50 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parsePAYER_NR();

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_position43 = $this->position;

            $_value42 = array();

            $_success = $this->parseA10();

            if ($_success) {
                $_value42[] = $this->value;

                $_success = $this->parseA5();
            }

            if ($_success) {
                $_value42[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value42[] = $this->value;

                $this->value = $_value42;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position43, $this->position - $_position43));
            }

            if ($_success) {
                $accountNr = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_position45 = $this->position;

            $_value44 = array();

            $_success = $this->parseN();

            if ($_success) {
                $_value44[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value44[] = $this->value;

                $this->value = $_value44;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position45, $this->position - $_position45));
            }

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_position47 = $this->position;

            $_value46 = array();

            $_success = $this->parseN();

            if ($_success) {
                $_value46[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value46[] = $this->value;

                $this->value = $_value46;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position47, $this->position - $_position47));
            }

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_position48 = $this->position;
            $_cut49 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position48;
                $this->value = null;
            }

            $this->cut = $_cut49;

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value50[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value50[] = $this->value;

            $this->value = $_value50;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$accountNr, &$id, &$info, &$comment, &$date) {
                // TODO fundera om jag kan dela upp denna i flera R_ så att jag kan använda ACCOUNT ...
                $accountNr = trim(ltrim($accountNr, '0'));

                return new MandateResponseNode(
                    $bg,
                    $payerNr,
                    new AccountNode($this->currentLineNr, $accountNr ?: $payerNr->getValue()),
                    $id,
                    new MessageNode($this->currentLineNr, "73.$info"),
                    new MessageNode($this->currentLineNr, "73.comment.$comment"),
                    $date ?: new \DateTimeImmutable('@0'),
                    $this->currentLineNr
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

        $_value53 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value53[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value53[] = $this->value;

            $_success = $this->parseBGC_CLEARING();
        }

        if ($_success) {
            $_value53[] = $this->value;

            $_position52 = $this->position;

            $_value51 = array();

            $_success = $this->parseN5();

            if ($_success) {
                $_value51[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value51[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value51[] = $this->value;

                $this->value = $_value51;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position52, $this->position - $_position52));
            }

            if ($_success) {
                $nrOfPosts = $this->value;
            }
        }

        if ($_success) {
            $_value53[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value53[] = $this->value;

            $this->value = $_value53;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrOfPosts) {
                return new ClosingNode($date, intval($nrOfPosts), $this->currentLineNr);
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

        $_position55 = $this->position;

        $_value54 = array();

        $_success = $this->parseN10();

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseN5();
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value54[] = $this->value;

            $this->value = $_value54;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position55, $this->position - $_position55));
        }

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new AccountNode($this->currentLineNr, ltrim($number, '0'));
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

    protected function parseBANKGIRO()
    {
        $_position = $this->position;

        if (isset($this->cache['BANKGIRO'][$_position])) {
            $_success = $this->cache['BANKGIRO'][$_position]['success'];
            $this->position = $this->cache['BANKGIRO'][$_position]['position'];
            $this->value = $this->cache['BANKGIRO'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseN10();

        if ($_success) {
            $number = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$number) {
                return new BankgiroNode($this->currentLineNr, ltrim($number, '0'));
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

        $_position57 = $this->position;

        $_value56 = array();

        $_success = $this->parseN5();

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value56[] = $this->value;

            $this->value = $_value56;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position57, $this->position - $_position57));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new BgcCustomerNumberNode($this->currentLineNr, ltrim($nr, '0'));
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

        $_position59 = $this->position;

        $_value58 = array();

        $_success = $this->parseN5();

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value58[] = $this->value;

            $this->value = $_value58;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position59, $this->position - $_position59));
        }

        if ($_success) {
            $date = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date) {
                return new \DateTimeImmutable($date . '000000');
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

    protected function parseID()
    {
        $_position = $this->position;

        if (isset($this->cache['ID'][$_position])) {
            $_success = $this->cache['ID'][$_position]['success'];
            $this->position = $this->cache['ID'][$_position]['position'];
            $this->value = $this->cache['ID'][$_position]['value'];

            return $_success;
        }

        $_value62 = array();

        $_position61 = $this->position;

        $_value60 = array();

        $_success = $this->parseA();

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
            $century = $this->value;
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseA10();

            if ($_success) {
                $number = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $this->value = $_value62;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$century, &$number) {
                return in_array($century, ['00', '99'])
                    ? new OrganizationIdNode($this->currentLineNr, $number)
                    : new PersonalIdNode($this->currentLineNr, $century.$number);
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

    protected function parsePAYER_NR()
    {
        $_position = $this->position;

        if (isset($this->cache['PAYER_NR'][$_position])) {
            $_success = $this->cache['PAYER_NR'][$_position]['success'];
            $this->position = $this->cache['PAYER_NR'][$_position]['position'];
            $this->value = $this->cache['PAYER_NR'][$_position]['value'];

            return $_success;
        }

        $_position64 = $this->position;

        $_value63 = array();

        $_success = $this->parseN10();

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseN5();
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value63[] = $this->value;

            $this->value = $_value63;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position64, $this->position - $_position64));
        }

        if ($_success) {
            $nr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nr) {
                return new PayerNumberNode($this->currentLineNr, ltrim($nr, '0'));
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

        $_position66 = $this->position;

        $_value65 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value65[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value65[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value65[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value65[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value65[] = $this->value;

            $this->value = $_value65;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position66, $this->position - $_position66));
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

        $_position68 = $this->position;

        $_value67 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value67[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value67[] = $this->value;

            $this->value = $_value67;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position68, $this->position - $_position68));
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

        $_position70 = $this->position;

        $_value69 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value69[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value69[] = $this->value;

            $this->value = $_value69;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position70, $this->position - $_position70));
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

    protected function parseA40()
    {
        $_position = $this->position;

        if (isset($this->cache['A40'][$_position])) {
            $_success = $this->cache['A40'][$_position]['success'];
            $this->position = $this->cache['A40'][$_position]['position'];
            $this->value = $this->cache['A40'][$_position]['value'];

            return $_success;
        }

        $_position72 = $this->position;

        $_value71 = array();

        $_success = $this->parseA20();

        if ($_success) {
            $_value71[] = $this->value;

            $_success = $this->parseA20();
        }

        if ($_success) {
            $_value71[] = $this->value;

            $this->value = $_value71;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position72, $this->position - $_position72));
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

        $_position74 = $this->position;

        $_value73 = array();

        $_success = $this->parseN();

        if ($_success) {
            $_value73[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value73[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value73[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value73[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value73[] = $this->value;

            $this->value = $_value73;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position74, $this->position - $_position74));
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

    protected function parseN10()
    {
        $_position = $this->position;

        if (isset($this->cache['N10'][$_position])) {
            $_success = $this->cache['N10'][$_position]['success'];
            $this->position = $this->cache['N10'][$_position]['position'];
            $this->value = $this->cache['N10'][$_position]['value'];

            return $_success;
        }

        $_position76 = $this->position;

        $_value75 = array();

        $_success = $this->parseN5();

        if ($_success) {
            $_value75[] = $this->value;

            $_success = $this->parseN5();
        }

        if ($_success) {
            $_value75[] = $this->value;

            $this->value = $_value75;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position76, $this->position - $_position76));
        }

        $this->cache['N10'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'N10');
        }

        return $_success;
    }

    protected function parseN20()
    {
        $_position = $this->position;

        if (isset($this->cache['N20'][$_position])) {
            $_success = $this->cache['N20'][$_position]['success'];
            $this->position = $this->cache['N20'][$_position]['position'];
            $this->value = $this->cache['N20'][$_position]['value'];

            return $_success;
        }

        $_position78 = $this->position;

        $_value77 = array();

        $_success = $this->parseN10();

        if ($_success) {
            $_value77[] = $this->value;

            $_success = $this->parseN10();
        }

        if ($_success) {
            $_value77[] = $this->value;

            $this->value = $_value77;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position78, $this->position - $_position78));
        }

        $this->cache['N20'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'N20');
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

        $_position80 = $this->position;

        $_value79 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value79[] = $this->value;

            $this->value = $_value79;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position80, $this->position - $_position80));
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

        $_position82 = $this->position;

        $_value81 = array();

        $_success = $this->parseS5();

        if ($_success) {
            $_value81[] = $this->value;

            $_success = $this->parseS5();
        }

        if ($_success) {
            $_value81[] = $this->value;

            $this->value = $_value81;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position82, $this->position - $_position82));
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

        $_position84 = $this->position;

        $_value83 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value83[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value83[] = $this->value;

            $this->value = $_value83;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position84, $this->position - $_position84));
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

    protected function parseEOL()
    {
        $_position = $this->position;

        if (isset($this->cache['EOL'][$_position])) {
            $_success = $this->cache['EOL'][$_position]['success'];
            $this->position = $this->cache['EOL'][$_position]['position'];
            $this->value = $this->cache['EOL'][$_position]['value'];

            return $_success;
        }

        $_value87 = array();

        $_position85 = $this->position;
        $_cut86 = $this->cut;

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
            $this->position = $_position85;
            $this->value = null;
        }

        $this->cut = $_cut86;

        if ($_success) {
            $_value87[] = $this->value;

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
            $_value87[] = $this->value;

            $this->value = $_value87;
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

    protected function parseEOR()
    {
        $_position = $this->position;

        if (isset($this->cache['EOR'][$_position])) {
            $_success = $this->cache['EOR'][$_position]['success'];
            $this->position = $this->cache['EOR'][$_position]['position'];
            $this->value = $this->cache['EOR'][$_position]['value'];

            return $_success;
        }

        $_value91 = array();

        $_value89 = array();
        $_cut90 = $this->cut;

        while (true) {
            $_position88 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

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
            $_value91[] = $this->value;

            $_success = $this->parseEOL();
        }

        if ($_success) {
            $_value91[] = $this->value;

            $this->value = $_value91;
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