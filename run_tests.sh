set -e
php build_parser.php
phpspec run
behat --stop-on-failure
readme-tester test README.md
