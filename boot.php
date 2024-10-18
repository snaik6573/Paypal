<?php
require 'vendor/autoload.php'; 

$host = '34.205.207.196';
$db = 'paypal'; 
$user = 'postgres'; 
$pass = 'canqualify'; 
$charset = 'utf8mb4';

$dsn = "pgsql:host=$host;dbname=$db;user=$user;password=$pass"; 
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
use Braintree\Gateway;

$gateway = new Gateway([
    'environment' => 'sandbox',
    'merchantId' => 'sk39ptqh27m5jnnv',
    'publicKey' => 'xk5mf5hjy5qv57yp',
    'privateKey' => '8a036ed45e5f5c0a6803468613c1926c',
]);
