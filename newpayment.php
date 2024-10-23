<?php
require 'vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '34.205.207.196';
$db = 'paypal'; 
$user = 'postgres'; 
$pass = 'canqualify'; 
$charset = 'utf8mb4';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

use Braintree\Gateway;

$gateway = new Gateway([
    'environment' => 'sandbox',
    'merchantId' => 'sk39ptqh27m5jnnv',
    'publicKey' => 'xk5mf5hjy5qv57yp',
    'privateKey' => '8a036ed45e5f5c0a6803468613c1926c',
]);

$customer_id = '54587814549';
$amount = $_POST['amount'];
$payment_method_nonce = $_POST['payment_method_nonce'] ?? null;
$firstname = $_POST['firstname'] ?? null;
$lastname = $_POST['lastname'] ?? null;

try {
    $paymentMethodResult = $gateway->paymentMethod()->create([
        'customerId' => $customer_id,
        'paymentMethodNonce' => $payment_method_nonce,
        'options' => [
            'verifyCard' => true 
        ]
    ]);

    if ($paymentMethodResult->success) {
        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodToken' => $paymentMethodResult->paymentMethod->token, 
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if ($result->success) {
            $transactionId = $result->transaction->id;
            $cardType = $result->transaction->paymentInstrumentType; 
            $status = $result->transaction->status; 

            $stmt = $pdo->prepare("INSERT INTO transactions (customer_id, firstname, lastname, amount, transaction_id, card_type, status) VALUES (:customer_id, :firstname, :lastname, :amount, :transaction_id, :card_type, :status)");
            $stmt->execute([
                'customer_id' => $customer_id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'card_type' => $cardType,
                'status' => $status
            ]);

            echo "Transaction successful! Transaction ID: " . $transactionId;
        } else {
            echo "Transaction failed: " . $result->message;
        }
    } else {
        echo "Payment method creation failed: " . $paymentMethodResult->message;
    }
} catch (Exception $e) {
    die("Error processing payment: " . $e->getMessage());
}
