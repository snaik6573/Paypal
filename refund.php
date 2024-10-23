<?php
require_once 'vendor/autoload.php';
use Braintree\Gateway;
$gateway = new Gateway([
    'environment' => 'sandbox',
    'merchantId' => 'sk39ptqh27m5jnnv',
    'publicKey' => 'xk5mf5hjy5qv57yp',
    'privateKey' => '8a036ed45e5f5c0a6803468613c1926c',
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionId = $_POST['transaction_id'];

    try {
        $result = $gateway->transaction()->refund($transactionId);

        if ($result->success) {
            echo "Transaction {$transactionId} has been refunded successfully.";
        } else {
            echo "Refund failed: " . implode(', ', $result->errors->deepAll());
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
