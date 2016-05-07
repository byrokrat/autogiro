<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Line;
use byrokrat\autogiro\Record\Record;

/**
 * Match and capture parts of a line into a Record
 */
class RecordReader
{
    /**
     * @var Matcher\Matcher
     */
    private $matchers = [];

    /**
     * @var callable Record builder
     */
    private $builder;

    /**
     * Inject matchers and builder
     *
     * The builder should take an array of values captured by registered
     * matchers and return a Record object.
     */
    public function __construct(array $matchers, callable $builder)
    {
        foreach ($matchers as $key => $matcher) {
            $this->addMatcher($key, $matcher);
        }
        $this->setBuilder($builder);
    }

    /**
     * Create Record from matched $line content
     */
    public function readRecord(Line $line): Record
    {
        $parts = [];

        foreach ($this->matchers as $key => $matcher) {
            $parts[$key] = $matcher->match($line);
        }

        return ($this->builder)($parts);
    }

    /**
     * Add content matcher
     */
    protected function addMatcher(string $key, Matcher\Matcher $matcher)
    {
        $this->matchers[$key] = $matcher;
    }

    /**
     * Set record builder
     */
    protected function setBuilder(callable $builder)
    {
        $this->builder = $builder;
    }
}
