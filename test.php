<?php

namespace byrokrat\autogiro;

include 'vendor/autoload.php';

$parser = new Parser\Parser(new Parser\Strategy\MandateResponseStrategy);

// TODO FileObject verkar vara onödigt. SplFileObject??
//$file = new FileObject(file_get_contents('TEMP_autogiro_exempelfiler_medgivandeavisering_sv.txt'));

try {
    $data = $parser->parse(new \SplFileObject('TEMP_autogiro_exempelfiler_medgivandeavisering_sv.txt'));
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
    die();
}

var_dump($data);
