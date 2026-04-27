<?php
$t = microtime(true);
$host = '127.0.0.1';
$port = 3306;
$db = 'pairprogramming';
$user = 'root';
$pass = '';

echo "Connecting to MySQL...\n";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 3]);
    echo "Connected successfully!\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
echo "Time taken: " . (microtime(true) - $t) . " seconds\n";
