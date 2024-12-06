<?php

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

require_once dirname(__DIR__, 1) . '/config.php';


echo ' qwerty ';

$io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());

$io->comment('testing abc');