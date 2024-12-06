<?php

use App\LeadsParser;
use App\Tools\File;

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->filesToDelete = [];
});

it('filters duplicates', function(string $filePath, array $expected) {
    $results = (new LeadsParser( File::read($filePath) ))->deduplicate();

    $this->filesToDelete[] = $results['updatedFilePath'];
    $this->filesToDelete[] = $results['logPath'];

    foreach($expected as $key => $count) {
        assertEquals($expected[$key], $results[$key]);
    }

})->with('parser-results');

afterEach(function() {
    foreach($this->filesToDelete as $file) {
        unlink($file);
    }
});