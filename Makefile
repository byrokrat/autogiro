COMPOSER_CMD=composer
PHIVE_CMD=phive

PHPSPEC_CMD=tools/phpspec
BEHAT_CMD=tools/behat
README_TESTER_CMD=tools/readme-tester
PHPSTAN_CMD=tools/phpstan
PHPCS_CMD=tools/phpcs

PHPEG_CMD=vendor/bin/phpeg

GRAMMAR=src/Parser/Grammar.php
PARSER_FILES:=$(shell find src/Parser/ -type f -name '*.php' ! -path $(GRAMMAR))

.DEFAULT_GOAL=all

.PHONY: all
all: $(GRAMMAR) test analyze

$(GRAMMAR): $(PARSER_FILES) vendor/installed
	$(PHPEG_CMD) generate src/Parser/Grammar.peg

.PHONY: clean
clean:
	rm -f $(GRAMMAR)
	rm -rf vendor
	rm -f composer.lock
	rm -rf tools
	rm -f phive.xml

.PHONY: test
test: phpspec behat docs

.PHONY: phpspec
phpspec: vendor/installed $(GRAMMAR) $(PHPSPEC_CMD)
	$(PHPSPEC_CMD) run

.PHONY: behat
behat: vendor/installed $(GRAMMAR) $(BEHAT_CMD)
	$(BEHAT_CMD) --stop-on-failure

.PHONY: docs
docs: vendor/installed $(GRAMMAR) $(README_TESTER_CMD)
	$(README_TESTER_CMD) README.md

.PHONY: analyze
analyze: phpstan phpcs

.PHONY: phpstan
phpstan: vendor/installed $(GRAMMAR) $(PHPSTAN_CMD)
	$(PHPSTAN_CMD) analyze -c phpstan.neon -l 7 src

.PHONY: phpcs
phpcs: $(PHPCS_CMD)
	$(PHPCS_CMD) src --standard=PSR2 --ignore=$(GRAMMAR)
	$(PHPCS_CMD) spec --standard=spec/ruleset.xml

vendor/installed: composer.json
	$(COMPOSER_CMD) install
	touch $@

$(PHPSPEC_CMD):
	$(PHIVE_CMD) install phpspec/phpspec --force-accept-unsigned

$(BEHAT_CMD):
	$(PHIVE_CMD) install behat/behat:3 --force-accept-unsigned

$(README_TESTER_CMD):
	$(PHIVE_CMD) install hanneskod/readme-tester:1 --force-accept-unsigned

$(PHPSTAN_CMD):
	$(PHIVE_CMD) install phpstan

$(PHPCS_CMD):
	$(PHIVE_CMD) install phpcs
