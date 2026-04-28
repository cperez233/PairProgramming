<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$code = "
fun main() {
    println(\"Translated to JS!\")
}
";

$payload = [
    'args' => '',
    'files' => [
        [
            'name' => 'File.kt',
            'text' => $code,
            'publicId' => ''
        ]
    ],
    'confType' => 'js'
];

$response = Http::post('https://api.kotlinlang.org/api/compiler/translate', $payload);

echo "Status: " . $response->status() . "\n";
echo "Body:\n" . $response->body() . "\n";
