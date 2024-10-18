<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use Braintree\ClientToken;
use Braintree\Exception;

Braintree\Configuration::environment('sandbox');
Braintree\Configuration::merchantId('sk39ptqh27m5jnnv');
Braintree\Configuration::publicKey('xk5mf5hjy5qv57yp');
Braintree\Configuration::privateKey('8a036ed45e5f5c0a6803468613c1926c');

header('Content-Type: application/json');

try {
    $clientToken = ClientToken::generate();
    echo json_encode($clientToken);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
