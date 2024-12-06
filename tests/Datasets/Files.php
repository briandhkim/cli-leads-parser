<?php 

const PATH_PREFIX = 'tests/TestFiles/';

dataset('file-type-validations', function () {
    return [
        'file does not exist' => [
            PATH_PREFIX . str_repeat('a', 5) . '.json',
            'Could not find the file.'
        ],
        'invalid file provided' => [
            PATH_PREFIX . 'invalidFileType.txt',
            'Please provide a .json file.'
        ],
        'file does not have any data' => [
            PATH_PREFIX . 'noData.json',
            'The provided file has no data.'
        ],
        'does not have a valid json' => [
            PATH_PREFIX . 'invalidJson.json',
            'The provided file does not have a valid JSON data.'
        ],
        'leads array is empty' => [
            PATH_PREFIX . 'emptyLeads.json',
            'There must be a minimum of 1 items in the array'
        ],
        'leads array has invalid schema' => [
            PATH_PREFIX . 'invalidLeadsSchema.json',
            'String value found, but an array is required'
        ],
        'json does not have leads array' => [
            PATH_PREFIX . 'invalidSchemaNoLeads.json',
            'The property leads is required'
        ],
        'leads does not have the _id field' => [
            PATH_PREFIX . 'leadsWithoutId.json',
            'The property _id is required'
        ],
        'valid file does not have any errors' => [
            PATH_PREFIX . 'leads.json',
            ''
        ]
    ];
});
