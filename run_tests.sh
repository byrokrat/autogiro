set -e
php build_parser.php
phpspec run
behat
readme-tester test README.md
