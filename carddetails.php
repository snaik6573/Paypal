<?php
require 'vendor/autoload.php';
use Braintree\Gateway;

$gateway = new Gateway([
    'environment' => 'sandbox',
    'merchantId' => 'sk39ptqh27m5jnnv',
    'publicKey' => 'xk5mf5hjy5qv57yp',
    'privateKey' => '8a036ed45e5f5c0a6803468613c1926c',
]);

$customer_id = '54587814549';
$payment_method_nonce = $_GET['payment_method_nonce'] ?? null;

if (!$payment_method_nonce) {
    die("Payment method nonce is not set.");
}

try {
    $customer = $gateway->customer()->find($customer_id);
    $payment_method = $gateway->paymentMethod()->find($payment_method_nonce);

    if (!$payment_method) {
        die("Payment method not found.");
    }

    $cardType = $payment_method->cardType;
    $last4 = $payment_method->last4;

} catch (Exception $e) {
    die("Error fetching customer or payment method: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment</title>
    <style>
        form {
            width: 300px;
            box-shadow: 0 4px 10px gray;
            margin-left: auto;
            margin-top: 30px;
            margin-right: auto;
            padding: 10px;
        }
        button {
            background-color: #008CBA;
            border: none;
            border-radius: 5px;
            height: 40px;
            font-size: 15px;
            font-weight: 600;
            width: 160px;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #04AA6D;
        }
    </style>
</head>
<body>
<form action="existingcardpayment.php" method="post">
        <?php if (!empty($payment_methods)): ?>
            <?php foreach ($payment_methods as $payment_method): ?>
                <label>
                    <input type="radio" name="payment_method_token" value="<?php echo htmlspecialchars($payment_method->token); ?>" required>
                    Card ending with <?php echo htmlspecialchars($payment_method->last4); ?>
                </label><br>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No existing payment methods found.</p>
        <?php endif; ?>
        
        <label for="amount">Amount</label><br><br>
        <input type="number" id="amount" name="amount" placeholder="Enter amount" min="1" step="0.01" required><br><br>
        
        <button type="submit">Pay with Braintree</button>
    </form>
</body>
</html>
