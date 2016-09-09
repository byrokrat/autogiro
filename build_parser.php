<?php

/**
 * Generate parser from peg file
 */
exec("vendor/bin/phpeg generate src/Grammar.peg");
