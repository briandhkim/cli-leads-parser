<?php

use App\Tools\File;

use function PHPUnit\Framework\assertEquals;

it('validates file provided', function(string $filePath, string $message) {
    $error = File::validate($filePath);

    assertEquals($message, $error);
})->with('file-type-validations');