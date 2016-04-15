<?php

namespace byrokrat\autogiro\Matcher;

class MatcherTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param  int    $start  The expected substr method start argument
     * @param  int    $length The expected substr method length argument
     * @param  string $return Requested substr method return value
     * @return byrokrat\autogiro\Line The created mock object
     */
    protected function mockLine($start, $length, $return)
    {
        $line = $this->prophesize('byrokrat\autogiro\Line');
        $line->substr($start, $length)->willReturn($return);
        return $line->reveal();
    }
}
