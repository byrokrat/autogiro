<?php
<<<CONFIG
packages:
    - "symfony/finder: ^4.0"
CONFIG;

$stopOnFailure = true;

echo "Simple melody script to test parsing all autogiro files in a directory.\n\n";
echo "Usage: melody run test_directory.php [DIRNAME]\n\n";

// Use local version of autogiro...
include __DIR__ . '/vendor/autoload.php';

$dir = $argv[1] ?? __DIR__;

echo "Scaning directory $dir\n\n";

$finder = Symfony\Component\Finder\Finder::create()
    ->in($dir)
    ->files()
    ->sortByName();

$parser = (new \byrokrat\autogiro\Parser\ParserFactory)->createParser();

$passCount = 0;
$failCount = 0;

foreach ($finder as $file) {
    try {
        $parser->parse($file->getContents());
        echo "PASS: {$file->getRelativePathname()}\n";
        $passCount++;
    } catch (Exception $e) {
        printf(
            "\nFAIL: %s \n\n%s\n\n",
            $file->getRelativePathname(),
            $e->getMessage()
        );
        $failCount++;
        if ($stopOnFailure) {
            die(1);
        }
    }
}

echo "\nDONE! $passCount files passed. $failCount failed.\n";
