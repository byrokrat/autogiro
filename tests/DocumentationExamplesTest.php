<?php

declare(strict_types = 1);

namespace byrokrat\autogiro;

use hanneskod\readmetester\PHPUnit\AssertReadme;

/**
 * Validate documentation examples
 *
 * @coversNothing
 */
class DocumentationExamplesTest extends \PHPUnit_Framework_TestCase
{
    public function testReadmeIntegrationTests()
    {
        if (!class_exists('hanneskod\readmetester\PHPUnit\AssertReadme')) {
            return $this->markTestSkipped('Readme-tester is not available.');
        }

        (new AssertReadme($this))->assertReadme('README.md');
    }
}
