PHPSPEC=vendor/bin/phpspec
BEHAT=vendor/bin/behat
README_TESTER=vendor/bin/readme-tester
PHPSTAN=vendor/bin/phpstan
PHPCS=vendor/bin/phpcs
PHPEG=vendor/bin/phpeg

COMPOSER_CMD=composer

GRAMMAR=src/Parser/Grammar.php

.DEFAULT_GOAL=all

.PHONY: all clean

all: $(GRAMMAR) test analyze

PARSER_FILES:=$(shell find src/Parser/ -type f -name '*.php' ! -path $(GRAMMAR))

$(GRAMMAR): $(PARSER_FILES) vendor-bin/installed
	$(PHPEG) generate src/Parser/Grammar.peg

clean:
	rm -f $(GRAMMAR)
	rm -rf vendor
	rm -rf vendor-bin
	rm -f composer.lock

#
# Tests and analysis
#

.PHONY: test analyze phpspec behat docs phpstan phpcs

test: phpspec behat docs

analyze: phpstan phpcs

phpspec: vendor-bin/installed
	$(PHPSPEC) run

behat: vendor-bin/installed $(GRAMMAR)
	$(BEHAT) --stop-on-failure

docs: vendor-bin/installed
	$(README_TESTER) README.md

phpstan: vendor-bin/installed
	$(PHPSTAN) analyze -c phpstan.neon -l 7 src

phpcs: vendor-bin/installed
	$(PHPCS) src --standard=PSR2 --ignore=$(GRAMMAR)
	$(PHPCS) spec --standard=spec/ruleset.xml

#
# Dependencies
#

vendor/installed: composer.json
	$(COMPOSER_CMD) validate --strict
	$(COMPOSER_CMD) install
	touch $@

vendor-bin/installed: vendor/installed
	$(COMPOSER_CMD) bin phpspec require phpspec/phpspec:">=5"
	$(COMPOSER_CMD) bin behat require behat/behat:^3
	$(COMPOSER_CMD) bin readme-tester require hanneskod/readme-tester:^1.0@beta
	$(COMPOSER_CMD) bin phpstan require "phpstan/phpstan:<2"
	$(COMPOSER_CMD) bin phpcs require squizlabs/php_codesniffer:^3
	$(COMPOSER_CMD) bin phpeg require scato/phpeg:^1.0
	touch $@
