<?php

namespace App\Tools;

use JsonSchema\Validator;

class File
{
    static $jsonSchema = <<<'JSON'
    {
        "type": "object",
        "properties": {
            "leads": {
                "type": "array",
                "items": {
                    "type": "object",
                    "properties": {
                        "_id": {
                            "type": "string"
                        },
                        "email": {
                            "type": "string"
                        },
                        "firstName": {
                            "type": "string"
                        },
                        "lastName": {
                            "type": "string"
                        },
                        "address": {
                            "type": "string"
                        },
                        "entryDate": {
                            "type": "string"
                        }
                    },
                    "required": [
                        "_id",
                        "email",
                        "firstName",
                        "lastName",
                        "address",
                        "entryDate"
                    ]
                },
                "minItems": 1
            }
        },
        "required": [
            "leads"
        ]
    }
    JSON;

    static function exists(string $path): bool
    {
        return file_exists($path);
    }

    static function read(string $path): array
    {
        $file = file_get_contents($path);

        return json_decode($file, 1);
    }

    static function write(string $path, string $fileName, $data)
    {
        if (!self::exists($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents(
            $path . $fileName,
            is_array($data) ? json_encode($data) : $data
        );
    }

    static function validate(string $filePath): string
    {
        if (!self::exists($filePath)) {
            return "Could not find the file.";
        }

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'json') {
            return 'Please provide a .json file.';
        }

        $data = file_get_contents($filePath);

        if (empty($data)) {
            return 'The provided file has no data.';
        }

        if (!json_validate($data)) {
            return 'The provided file does not have a valid JSON data.';
        }

        $json = json_decode($data);
        $validator = new Validator();
        $validator->validate($json, json_decode(self::$jsonSchema));

        if (!$validator->isValid()) {
            return $validator->getErrors()[0]['message'];
        }

        return '';
    }
}