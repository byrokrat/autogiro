<?php

declare(strict_types = 1);

namespace byrokrat\autogiro;

/**
 * Validate that all example files in files can be parsed
 *
 * @coversNothing
 */
class ParserIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function fileInfoProvider()
    {
        foreach (new \DirectoryIterator(__DIR__ . '/files/new_layout') as $fileInfo) {
            if ($fileInfo->getExtension() == 'txt') {
                yield [$fileInfo->getRealPath()];
            }
        }

        foreach (new \DirectoryIterator(__DIR__ . '/files/old_layout') as $fileInfo) {
            if ($fileInfo->getExtension() == 'txt') {
                yield [$fileInfo->getRealPath()];
            }
        }
    }

    /**
     * @dataProvider fileInfoProvider
     */
    public function testFiles(string $fname)
    {
        try {
            $tree = (new ParserFactory)->createParser()->parse(
                file_get_contents($fname)
            );
        } catch (\Exception $exception) {
            return $this->fail(
                sprintf(
                    "--\n%s/%s\n\n%s\n--\n",
                    basename(dirname($fname)),
                    basename($fname),
                    $exception->getMessage()
                )
            );
        }
    }
}
