<?php

namespace byrokrat\autogiro;

use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\MandateResponseNode;

class Grammar extends GrammarDependencies
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

        $_success = $this->parseMANDATE_RESPONSE_LAYOUT();

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

    protected function parseMANDATE_RESPONSE_LAYOUT()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_RESPONSE_LAYOUT'][$_position])) {
            $_success = $this->cache['MANDATE_RESPONSE_LAYOUT'][$_position]['success'];
            $this->position = $this->cache['MANDATE_RESPONSE_LAYOUT'][$_position]['position'];
            $this->value = $this->cache['MANDATE_RESPONSE_LAYOUT'][$_position]['value'];

            return $_success;
        }

        $_value6 = array();

        $_position1 = $this->position;
        $_cut2 = $this->cut;

        $this->cut = false;
        $_success = $this->parseGENERIC_OPENING_RECORD();

        if (!$_success && !$this->cut) {
            $this->position = $_position1;

            $_success = $this->parseMANDATE_RESPONSE_OPENING_RECORD_OLD();
        }

        $this->cut = $_cut2;

        if ($_success) {
            $opening = $this->value;
        }

        if ($_success) {
            $_value6[] = $this->value;

            $_value4 = array();
            $_cut5 = $this->cut;

            while (true) {
                $_position3 = $this->position;

                $this->cut = false;
                $_success = $this->parseMANDATE_RESPONSE_RECORD();

                if (!$_success) {
                    break;
                }

                $_value4[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position3;
                $this->value = $_value4;
            }

            $this->cut = $_cut5;

            if ($_success) {
                $mandates = $this->value;
            }
        }

        if ($_success) {
            $_value6[] = $this->value;

            $_success = $this->parseMANDATE_RESPONSE_CLOSING_RECORD();

            if ($_success) {
                $closing = $this->value;
            }
        }

        if ($_success) {
            $_value6[] = $this->value;

            $this->value = $_value6;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$opening, &$mandates, &$closing) {
                return new LayoutNode($opening, $closing, ...$mandates);
            });
        }

        $this->cache['MANDATE_RESPONSE_LAYOUT'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_RESPONSE_LAYOUT');
        }

        return $_success;
    }

    protected function parseMANDATE_RESPONSE_OPENING_RECORD_OLD()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_RESPONSE_OPENING_RECORD_OLD'][$_position])) {
            $_success = $this->cache['MANDATE_RESPONSE_OPENING_RECORD_OLD'][$_position]['success'];
            $this->position = $this->cache['MANDATE_RESPONSE_OPENING_RECORD_OLD'][$_position]['position'];
            $this->value = $this->cache['MANDATE_RESPONSE_OPENING_RECORD_OLD'][$_position]['value'];

            return $_success;
        }

        $_value7 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value7[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value7[] = $this->value;

            $_success = $this->parseBGC_CLEARING();
        }

        if ($_success) {
            $_value7[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value7[] = $this->value;

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
            $_value7[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value7[] = $this->value;

            $this->value = $_value7;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$bg) {
                return new OpeningNode('AG-MEDAVI', $date, '', $bg, $this->currentLineNr);
            });
        }

        $this->cache['MANDATE_RESPONSE_OPENING_RECORD_OLD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_RESPONSE_OPENING_RECORD_OLD');
        }

        return $_success;
    }

    protected function parseMANDATE_RESPONSE_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_RESPONSE_RECORD'][$_position])) {
            $_success = $this->cache['MANDATE_RESPONSE_RECORD'][$_position]['success'];
            $this->position = $this->cache['MANDATE_RESPONSE_RECORD'][$_position]['position'];
            $this->value = $this->cache['MANDATE_RESPONSE_RECORD'][$_position]['value'];

            return $_success;
        }

        $_value18 = array();

        if (substr($this->string, $this->position, strlen('73')) === '73') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('73'));
            $this->position += strlen('73');
        } else {
            $_success = false;

            $this->report($this->position, '\'73\'');
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_success = $this->parseBANKGIRO();

            if ($_success) {
                $bg = $this->value;
            }
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_position9 = $this->position;

            $_value8 = array();

            $_success = $this->parseN10();

            if ($_success) {
                $_value8[] = $this->value;

                $_success = $this->parseN5();
            }

            if ($_success) {
                $_value8[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value8[] = $this->value;

                $this->value = $_value8;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position9, $this->position - $_position9));
            }

            if ($_success) {
                $payerNr = $this->value;
            }
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_position11 = $this->position;

            $_value10 = array();

            $_success = $this->parseA10();

            if ($_success) {
                $_value10[] = $this->value;

                $_success = $this->parseA5();
            }

            if ($_success) {
                $_value10[] = $this->value;

                $_success = $this->parseA();
            }

            if ($_success) {
                $_value10[] = $this->value;

                $this->value = $_value10;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position11, $this->position - $_position11));
            }

            if ($_success) {
                $accountNr = $this->value;
            }
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_success = $this->parseID();

            if ($_success) {
                $id = $this->value;
            }
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_position13 = $this->position;

            $_value12 = array();

            $_success = $this->parseN();

            if ($_success) {
                $_value12[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value12[] = $this->value;

                $this->value = $_value12;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position13, $this->position - $_position13));
            }

            if ($_success) {
                $info = $this->value;
            }
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_position15 = $this->position;

            $_value14 = array();

            $_success = $this->parseN();

            if ($_success) {
                $_value14[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value14[] = $this->value;

                $this->value = $_value14;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position15, $this->position - $_position15));
            }

            if ($_success) {
                $comment = $this->value;
            }
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_position16 = $this->position;
            $_cut17 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE();

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position16;
                $this->value = null;
            }

            $this->cut = $_cut17;

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value18[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value18[] = $this->value;

            $this->value = $_value18;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$bg, &$payerNr, &$accountNr, &$id, &$info, &$comment, &$date) {
                $accountNr = trim(ltrim($accountNr, '0'));
                $payerNr = ltrim($payerNr, '0');

                return new MandateResponseNode(
                    $bg,
                    $payerNr,
                    $this->getAccountFactory()->createAccount($accountNr ?: $payerNr),
                    $id,
                    $this->getMessageFactory()->createMessage("73.$info"),
                    $this->getMessageFactory()->createMessage("73.comment.$comment"),
                    $date ?: new \DateTimeImmutable('@0'),
                    $this->currentLineNr
                );
            });
        }

        $this->cache['MANDATE_RESPONSE_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_RESPONSE_RECORD');
        }

        return $_success;
    }

    protected function parseMANDATE_RESPONSE_CLOSING_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['MANDATE_RESPONSE_CLOSING_RECORD'][$_position])) {
            $_success = $this->cache['MANDATE_RESPONSE_CLOSING_RECORD'][$_position]['success'];
            $this->position = $this->cache['MANDATE_RESPONSE_CLOSING_RECORD'][$_position]['position'];
            $this->value = $this->cache['MANDATE_RESPONSE_CLOSING_RECORD'][$_position]['value'];

            return $_success;
        }

        $_value21 = array();

        if (substr($this->string, $this->position, strlen('09')) === '09') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('09'));
            $this->position += strlen('09');
        } else {
            $_success = false;

            $this->report($this->position, '\'09\'');
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_success = $this->parseBGC_CLEARING();
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_position20 = $this->position;

            $_value19 = array();

            $_success = $this->parseN5();

            if ($_success) {
                $_value19[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value19[] = $this->value;

                $_success = $this->parseN();
            }

            if ($_success) {
                $_value19[] = $this->value;

                $this->value = $_value19;
            }

            if ($_success) {
                $this->value = strval(substr($this->string, $_position20, $this->position - $_position20));
            }

            if ($_success) {
                $nrOfPosts = $this->value;
            }
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value21[] = $this->value;

            $this->value = $_value21;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$nrOfPosts) {
                return new ClosingNode($date, intval($nrOfPosts), $this->currentLineNr);
            });
        }

        $this->cache['MANDATE_RESPONSE_CLOSING_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'MANDATE_RESPONSE_CLOSING_RECORD');
        }

        return $_success;
    }

    protected function parseGENERIC_OPENING_RECORD()
    {
        $_position = $this->position;

        if (isset($this->cache['GENERIC_OPENING_RECORD'][$_position])) {
            $_success = $this->cache['GENERIC_OPENING_RECORD'][$_position]['success'];
            $this->position = $this->cache['GENERIC_OPENING_RECORD'][$_position]['position'];
            $this->value = $this->cache['GENERIC_OPENING_RECORD'][$_position]['value'];

            return $_success;
        }

        $_value22 = array();

        if (substr($this->string, $this->position, strlen('01')) === '01') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('01'));
            $this->position += strlen('01');
        } else {
            $_success = false;

            $this->report($this->position, '\'01\'');
        }

        if ($_success) {
            $_value22[] = $this->value;

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
            $_value22[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseDATE();

            if ($_success) {
                $date = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseA20();

            if ($_success) {
                $layout = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseCUST_NR();

            if ($_success) {
                $custNr = $this->value;
            }
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

            $_success = $this->parseEOR();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $this->value = $_value22;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$date, &$layout, &$custNr, &$bg) {
                return new OpeningNode(rtrim($layout), $date, $custNr, $bg, $this->currentLineNr);
            });
        }

        $this->cache['GENERIC_OPENING_RECORD'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'GENERIC_OPENING_RECORD');
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
                return $this->getBankgiroFactory()->createAccount(ltrim($number, '0'));
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

        $_position24 = $this->position;

        $_value23 = array();

        $_success = $this->parseN5();

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value23[] = $this->value;

            $this->value = $_value23;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position24, $this->position - $_position24));
        }

        if ($_success) {
            $custNr = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$custNr) {
                return ltrim($custNr, '0');
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

        $_position26 = $this->position;

        $_value25 = array();

        $_success = $this->parseN5();

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value25[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value25[] = $this->value;

            $this->value = $_value25;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position26, $this->position - $_position26));
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

        $_value29 = array();

        $_position28 = $this->position;

        $_value27 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value27[] = $this->value;

            $this->value = $_value27;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position28, $this->position - $_position28));
        }

        if ($_success) {
            $century = $this->value;
        }

        if ($_success) {
            $_value29[] = $this->value;

            $_success = $this->parseA10();

            if ($_success) {
                $number = $this->value;
            }
        }

        if ($_success) {
            $_value29[] = $this->value;

            $this->value = $_value29;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$century, &$number) {
                if (in_array($century, ['00', '99'])) {
                    return $this->getOrganizationIdFactory()->create($number);
                }

                return $this->getPersonalIdFactory()->create($century.$number);
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

        $_position31 = $this->position;

        $_value30 = array();

        $_success = $this->parseA();

        if ($_success) {
            $_value30[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value30[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value30[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value30[] = $this->value;

            $_success = $this->parseA();
        }

        if ($_success) {
            $_value30[] = $this->value;

            $this->value = $_value30;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position31, $this->position - $_position31));
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

        $_position33 = $this->position;

        $_value32 = array();

        $_success = $this->parseA5();

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parseA5();
        }

        if ($_success) {
            $_value32[] = $this->value;

            $this->value = $_value32;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position33, $this->position - $_position33));
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

        $_position35 = $this->position;

        $_value34 = array();

        $_success = $this->parseA10();

        if ($_success) {
            $_value34[] = $this->value;

            $_success = $this->parseA10();
        }

        if ($_success) {
            $_value34[] = $this->value;

            $this->value = $_value34;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position35, $this->position - $_position35));
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

        $_position37 = $this->position;

        $_value36 = array();

        $_success = $this->parseN();

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value36[] = $this->value;

            $_success = $this->parseN();
        }

        if ($_success) {
            $_value36[] = $this->value;

            $this->value = $_value36;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position37, $this->position - $_position37));
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

        $_position39 = $this->position;

        $_value38 = array();

        $_success = $this->parseN5();

        if ($_success) {
            $_value38[] = $this->value;

            $_success = $this->parseN5();
        }

        if ($_success) {
            $_value38[] = $this->value;

            $this->value = $_value38;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position39, $this->position - $_position39));
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

        $_position41 = $this->position;

        $_value40 = array();

        $_success = $this->parseN10();

        if ($_success) {
            $_value40[] = $this->value;

            $_success = $this->parseN10();
        }

        if ($_success) {
            $_value40[] = $this->value;

            $this->value = $_value40;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position41, $this->position - $_position41));
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

        $_position43 = $this->position;

        $_value42 = array();

        $_success = $this->parseS();

        if ($_success) {
            $_value42[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value42[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value42[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value42[] = $this->value;

            $_success = $this->parseS();
        }

        if ($_success) {
            $_value42[] = $this->value;

            $this->value = $_value42;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position43, $this->position - $_position43));
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

        $_position45 = $this->position;

        $_value44 = array();

        $_success = $this->parseS5();

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseS5();
        }

        if ($_success) {
            $_value44[] = $this->value;

            $this->value = $_value44;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position45, $this->position - $_position45));
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

        $_position47 = $this->position;

        $_value46 = array();

        $_success = $this->parseS10();

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseS10();
        }

        if ($_success) {
            $_value46[] = $this->value;

            $this->value = $_value46;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position47, $this->position - $_position47));
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

        $_value50 = array();

        $_position48 = $this->position;
        $_cut49 = $this->cut;

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
            $this->position = $_position48;
            $this->value = null;
        }

        $this->cut = $_cut49;

        if ($_success) {
            $_value50[] = $this->value;

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
            $_value50[] = $this->value;

            $this->value = $_value50;
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

        $_value54 = array();

        $_value52 = array();
        $_cut53 = $this->cut;

        while (true) {
            $_position51 = $this->position;

            $this->cut = false;
            $_success = $this->parseA();

            if (!$_success) {
                break;
            }

            $_value52[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position51;
            $this->value = $_value52;
        }

        $this->cut = $_cut53;

        if ($_success) {
            $_value54[] = $this->value;

            $_success = $this->parseEOL();
        }

        if ($_success) {
            $_value54[] = $this->value;

            $this->value = $_value54;
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