<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

/**
 * Match and capture parts of a line into a Record
 */
class RecordReader
{
    /**
     * @var Mathcer\Matcher
     */
    private $matchers = [];

    /**
     * @var callable Record builder
     */
    private $builder;

    /**
     * Inject Record builder
     *
     * The builder should take an array of values captured by registered
     * matchers and return a Record object.
     */
    public function __construct(callable $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Capture content matching $matcher
     */
    public function match(string $key, Matcher\Matcher $matcher): self
    {
        $this->matchers[$key] = $matcher;
        return $this;
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
}
