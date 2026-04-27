<?php
$data = json_encode([
    'source_code' => 'fun main() { println("Hello from Paiza!") }',
    'language' => 'kotlin',
    'api_key' => 'guest',
]);

$ch = curl_init('https://api.paiza.io/runners/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
curl_close($ch);
$result = json_decode($response, true);
$id = $result['id'];
echo "Created job: $id\n";

// Poll until completed (max 20 seconds)
for ($i = 0; $i < 10; $i++) {
    sleep(2);
    $ch2 = curl_init("https://api.paiza.io/runners/get_details?id=$id&api_key=guest");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    $response2 = curl_exec($ch2);
    curl_close($ch2);
    $detail = json_decode($response2, true);
    echo "Poll $i: status={$detail['status']}\n";
    if ($detail['status'] === 'completed') {
        echo "STDOUT: {$detail['stdout']}\n";
        echo "STDERR: {$detail['stderr']}\n";
        echo "Exit: {$detail['exit_code']}\n";
        break;
    }
}
