<?php

namespace Bob\BuildConfig;

task('default', ['test', 'phpstan', 'sniff']);

desc('Run unit and feature tests');
task('test', ['phpspec', 'behat', 'examples']);

desc('Run phpspec unit tests');
task('phpspec', ['src/Parser/Grammar.php'], function() {
    sh('phpspec run', null, ['failOnError' => true]);
    println('Phpspec unit tests passed');
});

desc('Run behat feature tests');
task('behat', ['src/Parser/Grammar.php'], function() {
    sh('behat --stop-on-failure', null, ['failOnError' => true]);
    println('Behat feature tests passed');
});

desc('Tests documentation examples');
task('examples', ['src/Parser/Grammar.php'], function() {
    sh('readme-tester README.md', null, ['failOnError' => true]);
    println('Documentation examples valid');
});

desc('Run php code sniffer');
task('sniff', function() {
    sh('phpcs src --standard=PSR2 --ignore=src/Parser/Grammar.php', null, ['failOnError' => true]);
    println('Syntax checker on src/ passed');
    sh('phpcs spec --standard=spec/ruleset.xml', null, ['failOnError' => true]);
    println('Syntax checker on spec/ passed');
});

desc('Run statical analysis using phpstan');
task('phpstan', function() {
    sh('phpstan analyze -c phpstan.neon -l 7 src', null, ['failOnError' => true]);
    println('Phpstan analysis passed');
});

desc('Build parser');
task('build_parser', ['src/Parser/Grammar.php']);

$parserFiles = fileList('*.peg')->in([__DIR__ . '/src/Parser']);

fileTask('src/Parser/Grammar.php', $parserFiles, function() {
    sh('phpeg generate src/Parser/Grammar.peg', null, ['failOnError' => true]);
    println('Generated parser');
});

desc('Globally install development tools');
task('install_dev_tools', function() {
    sh('composer global require consolidation/cgr', null, ['failOnError' => true]);
    sh('cgr scato/phpeg:^1.0', null, ['failOnError' => true]);
    sh('cgr phpspec/phpspec', null, ['failOnError' => true]);
    sh('cgr behat/behat', null, ['failOnError' => true]);
    sh('cgr hanneskod/readme-tester:^1.0@beta', null, ['failOnError' => true]);
    sh('cgr squizlabs/php_codesniffer', null, ['failOnError' => true]);
    sh('cgr phpstan/phpstan', null, ['failOnError' => true]);
});
