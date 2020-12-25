COMPOSER_CMD=composer
PHIVE_CMD=phive

PHPSPEC_CMD=tools/phpspec
BEHAT_CMD=tools/behat
README_TESTER_CMD=tools/readme-tester
PHPSTAN_CMD=tools/phpstan
PHPCS_CMD=tools/phpcs
PHPEG_CMD=tools/phpeg

GRAMMAR=src/Parser/Grammar.php
PARSER_FILES:=$(shell find src/Parser/ -type f -name '*.php' ! -path $(GRAMMAR))

.DEFAULT_GOAL=all

.PHONY: all
all: $(GRAMMAR) test analyze

$(GRAMMAR): $(PARSER_FILES) vendor/installed $(PHPEG_CMD)
	$(PHPEG_CMD) generate src/Parser/Grammar.peg

# continous

.PHONY: clean
clean:
	rm -f $(GRAMMAR)
	rm -rf vendor
	rm -f composer.lock
	rm -rf tools

.PHONY: continuous-integration
continuous-integration: $(PHPSPEC_CMD) $(BEHAT_CMD) $(README_TESTER_CMD)
	$(PHPSPEC_CMD) run --verbose
	$(BEHAT_CMD)
	$(README_TESTER_CMD) README.md

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
	$(PHPSTAN_CMD) analyze -c phpstan.neon -l 8 src

.PHONY: phpcs
phpcs: $(PHPCS_CMD)
	$(PHPCS_CMD) src --standard=PSR2 --ignore=$(GRAMMAR)
	$(PHPCS_CMD) spec --standard=spec/ruleset.xml

vendor/installed: composer.json
	$(COMPOSER_CMD) install
	touch $@

tools/installed: .phive/phars.xml
	$(PHIVE_CMD) install --force-accept-unsigned --trust-gpg-keys CF1A108D0E7AE720,31C7E470E2138192,0FD3A3029E470F86
	touch $@

$(PHPSPEC_CMD): tools/installed
$(PHPSTAN_CMD): tools/installed
$(PHPCS_CMD): tools/installed
$(PHPEG_CMD): tools/installed
$(README_TESTER_CMD): tools/installed
$(BEHAT_CMD): tools/installed
