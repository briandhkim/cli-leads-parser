<?php

use App\LeadsParser;
use App\Tools\File;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

require_once './vendor/autoload.php';

$io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());

$exitWithMessage = function(string $message = 'Script finished, exiting.') use ($io)
{
    $io->info($message);
    exit;
};

$io->title('Starting duplicate leads parser');

/**
 * the leads data should be parsed by an object 
 * that would be able to handle the operation 
 * whether the data comes from cli or api
 * 
 * 1. read the file and get data
 * 2. pass the data to the parser
 * 3. output results
 */

$file = $argv[1] ?? null;

if (empty($file)) {
    $io->error('File path not provided');
    $exitWithMessage('Please provide the path to the JSON file as the first argument.');
}

$filePath = __DIR__ . "/$file";

$io->writeln('file path: ' . $filePath);

$validationError = File::validate($filePath);
if (!empty($validationError)) {
    $io->error($validationError);
    $exitWithMessage('Retry after addressing the error(s), exiting.');
}

$fileData = File::read($filePath);

try {
    $io->section('Parsing data');
    $results = (new LeadsParser($fileData))->deduplicate();

    $io->section('Results: ');
    $io->horizontalTable(
        ['File provided', '# of leads provided', '# of leads preserved', '# of duplicates removed', 'Log file', 'Updated leads file'],
        [
            [
                $file,
                $results['originalLeadsCount'],
                $results['leadsPreservedCount'],
                $results['duplicatesRemovedCount'],
                $results['logPath'],
                $results['updatedFilePath']
            ]
        ]
    );

} catch (Exception $e) {
    $io->error('Script errored out: ' . $e->getMessage());
}

$exitWithMessage();